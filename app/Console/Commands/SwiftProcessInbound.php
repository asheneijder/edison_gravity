<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwiftProcessInbound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swift:process-inbound {--disk=swift} {--inbound=inbound} {--outbound=outbound}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process inbound SWIFT MT/MX files and save to database';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\SwiftEngineService $engine)
    {
        $this->info("Starting SWIFT Engine Processing...");

        $disk = $this->option('disk');
        $inbound = $this->option('inbound');
        $outbound = $this->option('outbound');

        // Ensure directories exist
        \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($inbound);
        \Illuminate\Support\Facades\Storage::disk($disk)->makeDirectory($outbound);

        $stats = ['processed' => 0, 'duplicates' => 0, 'errors' => 0];

        $engine->processInboundFiles($disk, $inbound, $outbound, function ($type, $message) use (&$stats) {
            switch ($type) {
                case 'error':
                    $this->error($message);
                    $stats['errors']++;
                    break;
                case 'warn':
                    $this->warn($message);
                    if (str_contains($message, 'DUPLICATE'))
                        $stats['duplicates']++;
                    break;
                case 'info':
                    $this->info($message);
                    if (str_contains($message, 'ACCEPTED'))
                        $stats['processed']++;
                    break;
                default:
                    $this->line($message);
            }
        });

        // Notify Admins
        if ($stats['processed'] > 0 || $stats['errors'] > 0) {
            // Assuming Admin user checks notifications. Finding User ID 1 or all authorized admins.
            // For now, sending to User ID 1.
            $admin = \App\Models\User::find(1);
            if ($admin) {
                \Filament\Notifications\Notification::make()
                    ->title('Scheduled Processing Complete')
                    ->body("Processed: {$stats['processed']} | Skipped: {$stats['duplicates']} | Errors: {$stats['errors']}")
                    // Use Check Circle icon for success, X Circle for error?
                    ->icon($stats['errors'] > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                    ->color($stats['errors'] > 0 ? 'danger' : 'success')
                    ->sendToDatabase($admin);
            }
        }

        $this->newLine();
        $this->info("Processing complete.");
    }
}
