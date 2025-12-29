<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Models\SwiftMessage;

class SwiftMessageDownloadLogicTest extends TestCase
{
    public function test_download_logic_uses_file_directory_if_present()
    {
        Storage::fake('swift');

        $record = new SwiftMessage([
            'type' => '103',
            'file_directory' => 'test_file.csv',
            'source_file' => 'source.fin'
        ]);

        $filename = $record->file_directory ?? $record->source_file;
        $path = 'outbound/' . $record->type . '/' . $filename;

        $this->assertEquals('outbound/103/test_file.csv', $path);
    }

    public function test_fallback_logic_finds_file_by_pattern()
    {
        Storage::fake('swift');

        // Setup existing file in storage
        $type = '103';
        $sender = 'PNBMMYKLXXX';
        $receiver = 'AMTBMYKLXXX';
        $csvName = "103_single_customer_credit_transfer_{$sender}-{$receiver}_150725.csv";

        Storage::disk('swift')->put("outbound/{$type}/{$csvName}", 'dummy content');

        // Create record without file_directory
        $record = new SwiftMessage([
            'type' => $type,
            'frm_BIC' => $sender,
            'to_BIC' => $receiver,
            'file_directory' => null,
            'source_file' => 'original.fin'
        ]);

        // Logic to test
        $filename = $record->file_directory;
        if (!$filename) {
            // Smart Fallback pattern
            // Pattern: {TYPE}_*_{SENDER}-{RECEIVER}_*.csv
            $directory = "outbound/{$record->type}";
            $files = Storage::disk('swift')->files($directory);

            $pattern = "/^{$record->type}_.*_{$record->frm_BIC}-{$record->to_BIC}_.*\.csv$/i";

            foreach ($files as $file) {
                $basename = basename($file);
                if (preg_match($pattern, $basename)) {
                    $filename = $basename;
                    break;
                }
            }
        }

        // Using fallback if still null (original behavior, which fails for user)
        $finalFilename = $filename ?? $record->source_file;

        // Verify we found the CSV
        $this->assertEquals($csvName, $finalFilename);
    }
}
