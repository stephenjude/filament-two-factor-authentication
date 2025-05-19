<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait Defaults
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithRateLimiting;

    public function getUser(): FilamentUser
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception(
                __('filament-two-factor-authentication::components.base.wrong_user')
            );
        }

        return $user;
    }

    protected function sendRateLimitedNotification(TooManyRequestsException $exception): void
    {
        Notification::make()
            ->title(__('filament-two-factor-authentication::components.base.rate_limit_exceeded'))
            ->body(
                __(
                    'filament-two-factor-authentication::components.base.try_again',
                    ['seconds' => $exception->secondsUntilAvailable]
                )
            )
            ->danger()
            ->send();
    }
}
