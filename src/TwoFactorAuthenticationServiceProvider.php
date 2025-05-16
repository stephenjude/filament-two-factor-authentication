<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;
use Stephenjude\FilamentTwoFactorAuthentication\Testing\TestsFilamentTwoFactorAuthentication;

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
            ->hasCommands($this->getCommands())
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
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
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        FilamentAsset::register([
            Js::make('passkey-js', __DIR__.'/../resources/dist/filament-two-factor-authentication.js'),
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): string => Blade::render("<x-authenticate-passkey />"),
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Register Livewire Components
        Livewire::component('filament-two-factor-authentication::pages.challenge', Challenge::class);
        Livewire::component('filament-two-factor-authentication::pages.recovery', Recovery::class);
        Livewire::component('filament-two-factor-authentication::pages.setup', Setup::class);
        Livewire::component(
            'filament-two-factor-authentication::livewire.two-factor-authentication',
            TwoFactorAuthentication::class
        );

        // Testing
        Testable::mixin(new TestsFilamentTwoFactorAuthentication);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'stephenjude/filament-two-factor-authentication';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [

        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
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
