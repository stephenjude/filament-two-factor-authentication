<?php

use Illuminate\Support\Facades\Route;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

if (TwoFactorAuthenticationPlugin::get()->hasEnabledPasskeyAuthentication()) {
    Route::passkeys();
}
