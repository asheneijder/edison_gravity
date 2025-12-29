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

        $engine->processInboundFiles($disk, $inbound, $outbound, function ($type, $message) {
            switch ($type) {
                case 'error':
                    $this->error($message);
                    break;
                case 'warn':
                    $this->warn($message);
                    break;
                case 'info':
                    $this->info($message);
                    break;
                default:
                    $this->line($message);
            }
        });

        $this->newLine();
        $this->info("Processing complete.");
    }
}
