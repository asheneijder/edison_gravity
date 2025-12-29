<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    // ashraf29122025 : event trigger on login, dispatch async job 2 log it
    public function handle(Login $event): void
    {
        \App\Jobs\ProcessLoginLog::dispatch($event->user->getAuthIdentifier(), request()->ip(), request()->userAgent());
    }
}
