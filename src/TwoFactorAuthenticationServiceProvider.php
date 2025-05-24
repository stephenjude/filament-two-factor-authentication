<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Stephenjude\FilamentTwoFactorAuthentication\Livewire\PasskeyAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;

class TwoFactorAuthenticationServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-two-factor-authentication';

    public static string $viewNamespace = 'filament-two-factor-authentication';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->callSilently('vendor:publish', ['--tag' => 'passkeys-migrations']);
                    })
                    ->publishAssets()
                    ->publishMigrations()
                    ->publish('passkeys-migrations')
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('stephenjude/filament-two-factor-authentication');
            });

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(TwoFactorAuthenticationProviderContract::class, function ($app) {
            return new TwoFactorAuthenticationProvider(
                $app->make(Google2FA::class),
                $app->make(Repository::class)
            );
        });
    }

    public function packageBooted(): void
    {
        // Register Livewire Components
        Livewire::component('filament-two-factor-authentication::pages.challenge', Challenge::class);
        Livewire::component('filament-two-factor-authentication::pages.recovery', Recovery::class);
        Livewire::component('filament-two-factor-authentication::pages.setup', Setup::class);
        Livewire::component(
            'filament-two-factor-authentication::livewire.two-factor-authentication',
            TwoFactorAuthentication::class
        );
        Livewire::component(
            'filament-two-factor-authentication::livewire.passkey-authentication',
            PasskeyAuthentication::class
        );

        if (TwoFactorAuthenticationPlugin::get()->hasEnabledPasskeyAuthentication()) {
            $this->configurePasskey();

            FilamentAsset::register([
                Js::make('passkey-js', __DIR__.'/../resources/dist/filament-two-factor-authentication.js'),
            ]);

            FilamentView::registerRenderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn(): string => Blade::render('<x-filament-two-factor-authentication::passkey-login />'),
            );
        }
    }

    protected function configurePasskey(): void
    {
        $provider = config('auth.guards.'.filament()?->getCurrentPanel()?->getAuthGuard().'.provider');

        Config::set(
            key: 'passkeys.models.authenticatable',
            value: Config::get('auth.providers.'.$provider.'.model', 'App\\Models\\User')
        );

        $path = filament()?->getCurrentPanel()?->getPath();

        Config::set(
            key: 'passkeys.redirect_to_after_login',
            value: $path ? "/$path" : '/dashboard'
        );
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'add_two_factor_authentication_columns',
        ];
    }
}
