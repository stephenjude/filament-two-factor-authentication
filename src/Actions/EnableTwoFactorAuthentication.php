<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Actions;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Stephenjude\FilamentTwoFactorAuthentication\Contracts\TwoFactorAuthenticationProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Events\TwoFactorAuthenticationEnabled;

class EnableTwoFactorAuthentication
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
     * Enable two factor authentication for the user.
     */
    public function __invoke(User $user, bool $force = false): void
    {
        if (empty($user->two_factor_secret) || $force === true) {
            $user->forceFill([
                'two_factor_secret' => encrypt($this->provider->generateSecretKey()),
                'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                    return RecoveryCode::generate();
                })->all())),
            ])->save();

            $user->setTwoFactorChallengePassed();

            TwoFactorAuthenticationEnabled::dispatch($user);
        }
    }
}
