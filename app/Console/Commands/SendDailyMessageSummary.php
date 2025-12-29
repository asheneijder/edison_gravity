<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyMessageSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swift:daily-summary';

    protected $description = 'Send daily summary of new Swift messages to authorized users';

    public function handle()
    {
        $count = \App\Models\SwiftMessage::where('created_at', '>=', now()->subDay())->count();

        if ($count > 0) {
            $users = \App\Models\User::where('can_view_swift_messages', true)->get();

            foreach ($users as $user) {
                $user->notify(new \App\Notifications\DailyMessageSummary($count));
            }

            $this->info("Sent summary of {$count} messages to {$users->count()} users.");
        } else {
            $this->info('No new messages found.');
        }
    }
}
