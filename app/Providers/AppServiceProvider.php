<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\LogSuccessfulLogin::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\RevokeOtherSessions::class,
        );

        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::BODY_END,
            fn(): string => view('components.location-tracker'),
        );

        if (!config('app.debug') && filter_var(env('PROD_INDICATOR'), FILTER_VALIDATE_BOOLEAN)) {
            config(['app.name' => 'SWIFT-Engine']);
        } elseif (config('app.debug')) {
            config(['app.name' => 'SWIFT-Engine (DEV)']);
        }
    }
}
