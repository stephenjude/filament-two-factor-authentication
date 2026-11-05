<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecoveryCodeReplaced
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, public string $code) {}
}
