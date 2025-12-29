<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// ashraf29122025 : middleware to capture all user and admin activities
class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // ashraf29122025 : intercept request and dispatch logging job
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip debugbar or asset requests if needed, but for now capture all non-stateless
        if ($request->is('livewire/*') || $request->is('_debugbar/*')) {
            return $response;
        }

        $user = $request->user();
        $userId = $user ? $user->getAuthIdentifier() : null;
        $action = $request->method() . ' ' . $request->path();
        $description = $request->fullUrl();
        $ip = $request->ip();

        // Scope determination
        $scope = 'user';
        if ($request->is('admin*')) {
            $scope = 'admin';
        }

        // Dispatch logging job
        \App\Jobs\ProcessActivityLog::dispatch($userId, $action, $description, $ip, $scope);

        return $response;
    }
}
