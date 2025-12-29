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

        // Dynamic Mail Configuration
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('notification_settings')) {
                $mailSetting = \App\Models\NotificationSetting::where('is_active', true)->first();
                if ($mailSetting) {
                    config([
                        'mail.default' => $mailSetting->mailer ?? 'smtp',
                        'mail.mailers.smtp.transport' => $mailSetting->mailer ?? 'smtp',
                        'mail.mailers.smtp.host' => $mailSetting->host,
                        'mail.mailers.smtp.port' => $mailSetting->port,
                        'mail.mailers.smtp.encryption' => $mailSetting->encryption == 'null' ? null : $mailSetting->encryption,
                        'mail.mailers.smtp.username' => $mailSetting->username,
                        'mail.mailers.smtp.password' => $mailSetting->password,
                        'mail.from.address' => $mailSetting->from_address,
                        'mail.from.name' => $mailSetting->from_name ?? config('app.name'),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Fallback to .env in case of DB error
        }
    }
}
