<?php

namespace Mapexss\FilamentTwoFactorAuthentication\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Queue\SerializesModels;

class RecoveryCodeReplaced
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, public string $code) {}
}
