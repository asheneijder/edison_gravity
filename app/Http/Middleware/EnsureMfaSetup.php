<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfaSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Exclude MFA routes to prevent loops
        if ($request->routeIs('mfa.*')) {
            return $next($request);
        }

        // Check for MFA bypass
        if ($user->mfa_bypass) {
            return $next($request);
        }

        // If user has not set up MFA yet
        if (!$user->google2fa_secret) {
            return redirect()->route('mfa.setup');
        }

        // If secret exists but not verified in session -> Force Verify
        if (!session('mfa_verified')) {
            return redirect()->route('mfa.verify');
        }

        return $next($request);
    }
}
