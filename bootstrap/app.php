<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\LogActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ashraf29122025 : Global handler for 403 / Authorization exceptions to show toast instead of error page
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This function has been disable.'], 403);
            }

            \Filament\Notifications\Notification::make()
                ->title('This function has been disable.')
                ->warning()
                ->duration(10000)
                ->send();

            return back();
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This function has been disable.'], 403);
            }

            \Filament\Notifications\Notification::make()
                ->title('This function has been disable.')
                ->warning()
                ->duration(10000)
                ->send();

            return back();
        });
    })->create();
