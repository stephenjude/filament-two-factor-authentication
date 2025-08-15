<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Actions;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User;
use Illuminate\Validation\ValidationException;
use Stephenjude\FilamentTwoFactorAuthentication\Contracts\TwoFactorAuthenticationProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Events\TwoFactorAuthenticationConfirmed;

class ConfirmTwoFactorAuthentication
{
    /**
     * The two factor authentication provider.
     */
    protected TwoFactorAuthenticationProvider $provider;

    /**
     * Create a new action instance.
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Confirm the two factor authentication configuration for the user.
     */
    public function __invoke(User $user, string $code): void
    {
        if (empty($user->two_factor_secret) ||
            empty($code) ||
            ! $this->provider->verify(decrypt($user->two_factor_secret), $code)) {
            throw ValidationException::withMessages([
                'data.code' => __('filament-two-factor-authentication::actions.confirm_two_factor_authentication.wrong_code'),
            ]);
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        TwoFactorAuthenticationConfirmed::dispatch($user);
    }
}
