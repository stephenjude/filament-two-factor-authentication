<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

abstract class BaseLivewireComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithRateLimiting;

    public function getUser(): FilamentUser
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception(
                __('The authenticated user object must be a Filament Auth model to allow the profile page to update it.')
            );
        }

        return $user;
    }

    protected function sendRateLimitedNotification(TooManyRequestsException $exception): void
    {
        Notification::make()
            ->title(__('Too many requests'))
            ->body(__('Please try again in :seconds seconds', ['seconds' => $exception->secondsUntilAvailable]))
            ->danger()
            ->send();
    }
}
