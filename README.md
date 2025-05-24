![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/banner.jpg)

# Filament Two Factor Authentication (2FA)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stephenjude/filament-two-factor-authentication.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-two-factor-authentication)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-two-factor-authentication/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stephenjude/filament-two-factor-authentication/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-two-factor-authentication/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stephenjude/filament-two-factor-authentication/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stephenjude/filament-two-factor-authentication.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-two-factor-authentication)

Add two-factor authentication to new and existing Filament applications.

## Learn More
[Filament Two-Factor Authentication Demo](https://www.youtube.com/watch?v=zLqKFsAmEaQ) — Filament Daily 

## Installation

Below, you'll find documentation on installing this plugin. If you have any questions, find a bug, need support, or have
a feature request, please don't hesitate to reach out to me at stephenjudesuccess@gmail.com.

You can install the package via composer:

```bash
composer require stephenjude/filament-two-factor-authentication
```

Add the `TwoFactorAuthenticatable` trait to your application's authentication model and implement the `HasPasskeys` trait:

```php
namespace App\Models;

use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticatable;

//...

class User extends Authenticatable implements FilamentUser, HasPasskeys
{
    use TwoFactorAuthenticatable;
    
    //...

```
> ⚠️ Passkey implementation is using the [spatie/laravel-passkeys](https://github.com/spatie/laravel-passkeys) package under the hood.

Install the plugin migration using:
```bash
php artisan filament-two-factor-authentication:install
```

Optionally, you can publish the views using
```bash
php artisan vendor:publish --tag="filament-two-factor-authentication-views"
```

## Plugin Configuration
Add two-factor authentication plugin to a panel by instantiating the plugin class and passing it to the plugin() method of the configuration:

```php
...
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;
 
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            TwoFactorAuthenticationPlugin::make()
                    ->enableTwoFactorAuthentication() // Enable Google 2FA
                    ->enablePasskeyAuthentication() // Enable Passkey
                    ->addTwoFactorMenuItem() // Add 2FA menu item
                    ->forceTwoFactorSetup() // Force 2FA setup
        ])
}
...
```

### Advanced Configurations

```php
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\ForceTwoFactorSetup;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\TwoFactorChallenge;

TwoFactorAuthenticationPlugin::make()
        ->enableTwoFactorAuthentication(
            condition:  true, // Enable Google 2FA 
            challengeMiddleware:  TwoFactorChallenge::class, // Middleware to challenge user with 2FA
        ) 
        ->enablePasskeyAuthentication(
            condition:  true, // Enable Passkey 
        ) 
        ->forceTwoFactorSetup(
            condition:  true, // Force 2FA setup for all users
            requiresPassword:  true, // Require password during setup
            forceMiddleware:  ForceTwoFactorSetup::class, // Middleware to enforce 2FA
        )
        ->addTwoFactorMenuItem(
            condition:  true, // Show 2FA on the user menu item
            label:  '2FA', // Menu item label   
            icon:  'heroicon-s-key', // Menu item icon
        )
])
```

### Custom Settings Page
If your application already has a user profile page, you can add a 2FA settings to your profile page view:

```php
<x-filament-panels::page>
    @livewire(\Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication::class)

    @livewire(\Stephenjude\FilamentTwoFactorAuthentication\Livewire\PasskeyAuthentication::class)
</x-filament-panels::page>
```
## Events
This package dispatches events which your application can subscribe to. You can listen to these events inside your EventServiceProvider class:

```php
use Stephenjude\FilamentTwoFactorAuthentication\Events\{RecoveryCodeReplaced,RecoveryCodesGenerated,TwoFactorAuthenticationChallenged,TwoFactorAuthenticationConfirmed,TwoFactorAuthenticationDisabled,TwoFactorAuthenticationEnabled,TwoFactorAuthenticationFailed,ValidTwoFactorAuthenticationCodeProvided};

protected $listen = [
    TwoFactorAuthenticationChallenged::class => [
        // Dispatched when a user is required to enter 2FA code during login.
    ],
    TwoFactorAuthenticationFailed::class => [
        // Dispatched when a user provides incorrect 2FA code or recovery code during login.
    ],
    ValidTwoFactorAuthenticationCodeProvided::class => [
        // Dispatched when a user provides a valid 2FA code during login.
    ]
    TwoFactorAuthenticationConfirmed::class => [
        // Dispatched when a user confirms code during 2FA setup.
    ],
    TwoFactorAuthenticationEnabled::class => [
        // Dispatched when a user enables 2FA.
    ],
    TwoFactorAuthenticationDisabled::class => [
        // Dispatched when a user disables 2FA.
    ],
    RecoveryCodeReplaced::class => [
        // Dispatched after a user's recovery code is replaced.
    ],
    RecoveryCodesGenerated::class => [
        // Dispatched after a user's recovery codes are generated.
    ],
];
```

## Screenshot
![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/1.jpeg)
#### 2FA Authentication

![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/2.jpeg)
#### 2FA Recovery

![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/3.jpeg)
#### 2FA Disabled

![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/5.png)
#### 2FA Setup 

![Screenshot](https://raw.githubusercontent.com/stephenjude/filament-two-factor-authentication/main/art/4.jpeg)
#### 2FA Enabled (Recovery Codes)


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [stephenjude](https://github.com/stephenjude)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
