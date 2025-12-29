<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ActivityLog;
use Stevebauman\Location\Facades\Location;

// ashraf29122025 : job to process activity log in background
class ProcessActivityLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $action;
    public $description;
    public $ip;
    public $scope;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $action, $description, $ip, $scope)
    {
        $this->userId = $userId;
        $this->action = $action;
        $this->description = $description;
        $this->ip = $ip;
        $this->scope = $scope;
    }

    /**
     * Execute the job.
     */
    // ashraf29122025 : handle logic to get location and save to db
    public function handle(): void
    {
        // ashraf29122025 : handle local ip for testing
        $ipToUse = ($this->ip === '127.0.0.1' || $this->ip === '::1') ? '115.132.0.0' : $this->ip;

        $locationData = null;
        if ($position = Location::get($ipToUse)) {
            $locationData = $position->toArray();
        }

        ActivityLog::create([
            'user_id' => $this->userId,
            'action' => $this->action,
            'description' => $this->description,
            'ip_address' => $this->ip,
            'location' => $locationData,
            'scope' => $this->scope,
        ]);
    }
}
