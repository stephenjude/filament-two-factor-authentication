<?php

namespace Mapexss\FilamentTwoFactorAuthentication\Actions;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Mapexss\FilamentTwoFactorAuthentication\Events\RecoveryCodesGenerated;

class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     */
    public function __invoke(User $user): void
    {
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(
                json_encode(
                    Collection::times(8, function () {
                        return RecoveryCode::generate();
                    })->all()
                )
            ),
        ])->save();

        RecoveryCodesGenerated::dispatch($user);
    }
}
