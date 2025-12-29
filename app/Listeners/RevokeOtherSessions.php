<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// ashraf29122025 : listener to enforce single session policy
class RevokeOtherSessions
{
    /**
     * Handle the event.
     */
    // ashraf29122025 : remove other sessions upon login
    public function handle(Login $event): void
    {
        // Get the current session ID
        $currentSessionId = Session::getId();

        // Delete all other sessions for this user
        DB::table('sessions')
            ->where('user_id', $event->user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
