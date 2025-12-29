<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\LoginLog;
use Stevebauman\Location\Facades\Location;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLoginLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $ip;
    public $userAgent;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     */
    // ashraf29122025 : job run in bg, get location api & save everything 2 db
    public function handle(): void
    {
        if ($position = Location::get($this->ip)) {
            $locationData = $position->toArray();
        } else {
            $locationData = null;
        }

        LoginLog::create([
            'user_id' => $this->userId,
            'ip_address' => $this->ip,
            'user_agent' => $this->userAgent,
            'location' => $locationData,
        ]);
    }
}
