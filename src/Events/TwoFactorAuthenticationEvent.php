<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;

abstract class TwoFactorAuthenticationEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user) {}
}
