<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stephenjude\FilamentTwoFactorAuthentication\FilamentTwoFactorAuthentication
 */
class FilamentTwoFactorAuthentication extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Stephenjude\FilamentTwoFactorAuthentication\FilamentTwoFactorAuthentication::class;
    }
}
