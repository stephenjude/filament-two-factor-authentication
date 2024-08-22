# Filament Two Factor Authentication

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stephenjude/filament-two-factor-authentication.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-two-factor-authentication)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-two-factor-authentication/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stephenjude/filament-two-factor-authentication/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stephenjude/filament-two-factor-authentication/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stephenjude/filament-two-factor-authentication/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stephenjude/filament-two-factor-authentication.svg?style=flat-square)](https://packagist.org/packages/stephenjude/filament-two-factor-authentication)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require stephenjude/filament-two-factor-authentication
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-two-factor-authentication-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-two-factor-authentication-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-two-factor-authentication-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentTwoFactorAuthentication = new Stephenjude\FilamentTwoFactorAuthentication();
echo $filamentTwoFactorAuthentication->echoPhrase('Hello, Stephenjude!');
```

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
