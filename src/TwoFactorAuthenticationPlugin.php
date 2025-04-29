<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Route;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\ChallengeTwoFactor;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\EnforceTwoFactorSetup;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;

class TwoFactorAuthenticationPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool $hasEnforcedTwoFactorSetup = false;

    protected string $enforceTwoFactorSetupMiddleware = EnforceTwoFactorSetup::class;

    protected string | bool $twoFactorChallengeMiddleware = ChallengeTwoFactor::class;

    protected bool $hasTwoFactorMenuItem = false;

    protected ?string $twoFactorMenuItemLabel = null;

    protected ?string $twoFactorMenuItemIcon = null;

    protected bool | Closure $isPasswordRequiredForEnable = true;

    protected bool | Closure $isPasswordRequiredForDisable = true;

    protected bool | Closure $isPasswordRequiredForRegenerateRecoveryCodes = true;

    public function getId(): string
    {
        return 'filament-two-factor-authentication';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->routes(fn () => [
                Route::get('/two-factor-challenge', Challenge::class)->name('two-factor.challenge'),
                Route::get('/two-factor-recovery', Recovery::class)->name('two-factor.recovery'),
                Route::get('/two-factor-setup', Setup::class)->name('two-factor.setup'),
            ]);

        if ($this->hasTwoFactorMenuItem()) {
            $panel
                ->userMenuItems([
                    MenuItem::make()
                        ->label($this->twoFactorMenuItemLabel ?? fn () => __('filament-two-factor-authentication::plugin.user_menu_item_label'))
                        ->url(fn (): string => Filament::getCurrentPanel()->route('two-factor.setup'))
                        ->icon($this->twoFactorMenuItemIcon ?? 'heroicon-o-lock-closed'),
                ]);
        }

        if ($this->twoFactorChallengeMiddleware !== false) {
            $panel
                ->authMiddleware([
                    $this->twoFactorChallengeMiddleware,
                ]);
        }

        if ($this->hasEnforcedTwoFactorSetup()) {
            $panel
                ->authMiddleware([
                    $this->enforceTwoFactorSetupMiddleware,
                ]);
        }
    }

    public function requirePasswordWhenEnabling(bool | Closure $condition = true): static
    {
        $this->isPasswordRequiredForEnable = $condition;

        return $this;
    }

    public function requirePasswordWhenDisabling(bool | Closure $condition = true): static
    {
        $this->isPasswordRequiredForDisable = $condition;

        return $this;
    }

    public function requirePasswordWhenRegeneratingRecoveryCodes(bool | Closure $condition = true): static
    {
        $this->isPasswordRequiredForRegenerateRecoveryCodes = $condition;

        return $this;
    }

    public function isPasswordRequiredForRegenerateRecoveryCodes(): bool
    {
        return $this->evaluate($this->isPasswordRequiredForRegenerateRecoveryCodes);
    }

    public function isPasswordRequiredForEnable(): bool
    {
        return $this->evaluate($this->isPasswordRequiredForEnable);
    }

    public function isPasswordRequiredForDisable(): bool
    {
        return $this->evaluate($this->isPasswordRequiredForDisable);
    }

    public function setChallengeTwoFactorMiddleware(string | bool $middleware = ChallengeTwoFactor::class): static
    {
        $this->twoFactorChallengeMiddleware = $middleware;

        return $this;
    }

    public function getChallengeTwoFactorMiddleware(): string
    {
        return $this->twoFactorChallengeMiddleware;
    }

    public function enforceTwoFactorSetup(bool $condition = true, string $middleware = EnforceTwoFactorSetup::class): static
    {
        $this->hasEnforcedTwoFactorSetup = $condition;
        $this->enforceTwoFactorSetupMiddleware = $middleware;

        return $this;
    }

    public function getEnforceTwoFactorSetupMiddleware(): string
    {
        return $this->enforceTwoFactorSetupMiddleware;
    }

    public function hasEnforcedTwoFactorSetup(): bool
    {
        return $this->hasEnforcedTwoFactorSetup;
    }

    public function addTwoFactorMenuItem(bool $condition = true, ?string $label = null, ?string $icon = null): static
    {
        $this->hasTwoFactorMenuItem = $condition;

        $this->twoFactorMenuItemLabel = $label;

        return $this;
    }

    public function hasTwoFactorMenuItem(): bool
    {
        return $this->hasTwoFactorMenuItem;
    }

    public function boot(Panel $panel): void {}

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
}
