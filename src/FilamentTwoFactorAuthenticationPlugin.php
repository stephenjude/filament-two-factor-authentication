<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\EditProfile;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\TwoFactorChallenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\TwoFactorLogin;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\TwoFactorRecovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\TwoFactorSetup;

class FilamentTwoFactorAuthenticationPlugin implements Plugin
{
    protected bool $hasEnforcedTwoFactorAuthenticationSetup = false;

    public function getId(): string
    {
        return 'filament-two-factor-authentication';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->login(TwoFactorLogin::class)
            ->profile(page: EditProfile::class, isSimple: false)
            ->routes(fn() => [
                Route::get('/two-factor-challenge', TwoFactorChallenge::class)->name('two-factor.challenge'),
                Route::get('/two-factor-recovery', TwoFactorRecovery::class)->name('two-factor.recovery'),
                Route::get('/two-factor-setup', TwoFactorSetup::class)->name('two-factor.setup'),
            ]);

        if ($this->hasEnforcedTwoFactorAuthenticationSetup()) {
            $panel
                ->authMiddleware([
                    EnforceTwoFactorAuthenticationSetup::class
                ]);
        }
    }

    public function enforceTwoFactorAuthenticationSetup(bool $condition = true): static
    {
        $this->hasEnforcedTwoFactorAuthenticationSetup = $condition;

        return $this;
    }

    public function hasEnforcedTwoFactorAuthenticationSetup(): bool
    {
        return $this->hasEnforcedTwoFactorAuthenticationSetup;
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function routes(): array
    {
        return [
            Route::get('/two-factor-challenge', TwoFactorChallenge::class)->name('two-factor.challenge'),
            Route::get('/two-factor-recovery', TwoFactorRecovery::class)->name('two-factor.recovery'),
            Route::get('/two-factor-setup', TwoFactorSetup::class)->name('two-factor.setup'),
        ];
    }
}
