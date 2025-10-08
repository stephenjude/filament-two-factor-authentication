<?php

namespace Mapexss\FilamentTwoFactorAuthentication\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;

abstract class BaseSimplePage extends SimplePage implements HasSchemas
{
    use InteractsWithFormActions;
    use InteractsWithSchemas;
    use WithRateLimiting;

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(
                __('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ])
            )
            ->body(
                array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __(
                    'filament-panels::pages/auth/login.notifications.throttled.body',
                    [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => $exception->minutesUntilAvailable,
                    ]
                ) : null
            )
            ->danger();
    }
}
