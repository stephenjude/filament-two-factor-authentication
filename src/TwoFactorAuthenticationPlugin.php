<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Route;
use JetBrains\PhpStorm\Deprecated;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\ChallengeTwoFactor;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\EnforceTwoFactorSetup;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;

class TwoFactorAuthenticationPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool $enablePasskeyAuthentication = false;

    protected bool $enableTwoFactorAuthentication = false;

    protected bool $hasEnforcedTwoFactorSetup = false;

    protected bool $twoFactorSetupRequiresPassword = false;

    protected string $enforceTwoFactorSetupMiddleware = EnforceTwoFactorSetup::class;

    protected string | bool $twoFactorChallengeMiddleware = ChallengeTwoFactor::class;

    protected bool $hasTwoFactorMenuItem = false;

    protected ?string $twoFactorMenuItemLabel = null;

    protected ?string $twoFactorMenuItemIcon = null;

    #[Deprecated('Use the `twoFactorSetupRequiresPassword` property instead.')]
    protected bool | Closure $isPasswordRequiredForEnable = true;

    #[Deprecated('Use the `twoFactorSetupRequiresPassword` property instead.')]
    protected bool | Closure $isPasswordRequiredForDisable = true;

    #[Deprecated('Use the `twoFactorSetupRequiresPassword` property instead.')]
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
                        ->label(
                            $this->twoFactorMenuItemLabel ?? fn () => __(
                                'filament-two-factor-authentication::plugin.user_menu_item_label'
                            )
                        )
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

    #[Deprecated('Use enableTwoFactorAuthentication instead')]
    public function requirePasswordWhenEnabling(bool | Closure $condition = true): static
    {
        $this->isPasswordRequiredForEnable = $this->evaluate($condition);

        return $this;
    }

    #[Deprecated('Use enableTwoFactorAuthentication instead')]
    public function requirePasswordWhenDisabling(bool | Closure $condition = true): static
    {
        $this->isPasswordRequiredForDisable = $this->evaluate($condition);

        return $this;
    }

    #[Deprecated('Use enableTwoFactorAuthentication instead')]
    public function requirePasswordWhenRegeneratingRecoveryCodes(Closure | bool $condition = true): static
    {
        $this->isPasswordRequiredForRegenerateRecoveryCodes = $this->evaluate($condition);

        return $this;
    }

    #[Deprecated('Use twoFactorSetupRequiresPassword instead')]
    public function isPasswordRequiredForRegenerateRecoveryCodes(): bool
    {
        return $this->isPasswordRequiredForRegenerateRecoveryCodes;
    }

    #[Deprecated('Use twoFactorSetupRequiresPassword instead')]
    public function isPasswordRequiredForEnable(): bool
    {
        return $this->isPasswordRequiredForEnable;
    }

    #[Deprecated('Use twoFactorSetupRequiresPassword instead')]
    public function isPasswordRequiredForDisable(): bool
    {
        return $this->isPasswordRequiredForDisable;
    }

    public function twoFactorSetupRequiresPassword(): bool
    {
        return $this->twoFactorSetupRequiresPassword;
    }

    #[Deprecated('Use enableTwoFactorAuthentication(challengeMiddleware:ChallengeTwoFactor::class) instead')]
    public function setChallengeTwoFactorMiddleware(Closure | string | bool $middleware = ChallengeTwoFactor::class): static
    {
        $this->twoFactorChallengeMiddleware = $this->evaluate($middleware);

        return $this;
    }

    #[Deprecated('Use getTwoFactorChallengeMiddleware() instead')]
    public function getChallengeTwoFactorMiddleware(): string
    {
        return $this->twoFactorChallengeMiddleware;
    }

    public function getTwoFactorChallengeMiddleware(): string
    {
        return $this->twoFactorChallengeMiddleware;
    }

    #[Deprecated('Use enableTwoFactorAuthentication(forced:true) instead')]
    public function enforceTwoFactorSetup(
        Closure | bool $condition = true,
        Closure | string $middleware = EnforceTwoFactorSetup::class
    ): static {
        $this->hasEnforcedTwoFactorSetup = $this->evaluate($condition);

        $this->enforceTwoFactorSetupMiddleware = $this->evaluate($middleware);

        return $this;
    }

    public function enableTwoFactorAuthentication(
        Closure | bool $condition = true,
        Closure | bool $forced = true,
        Closure | bool $requiresPassword = true,
        Closure | string $forceMiddleware = EnforceTwoFactorSetup::class,
        Closure | string $challengeMiddleware = ChallengeTwoFactor::class,
        Closure | bool $addMenuItem = true,
        Closure | string | null $menuItemLabel = null,
        Closure | string | null $menuItemIcon = null,
    ): static {
        $this->enableTwoFactorAuthentication = $this->evaluate($condition);

        $this->hasEnforcedTwoFactorSetup = $this->evaluate($forced);

        $this->twoFactorSetupRequiresPassword = $this->evaluate($requiresPassword);

        $this->twoFactorChallengeMiddleware = $this->evaluate($challengeMiddleware);

        $this->enforceTwoFactorSetupMiddleware = $this->evaluate($forceMiddleware);

        $this->hasTwoFactorMenuItem = $this->evaluate($addMenuItem);

        $this->twoFactorMenuItemLabel = $this->evaluate($menuItemLabel);

        $this->twoFactorMenuItemIcon = $this->evaluate($menuItemIcon);

        $this->isPasswordRequiredForEnable = $this->evaluate($requiresPassword);
        $this->isPasswordRequiredForDisable = $this->evaluate($requiresPassword);
        $this->isPasswordRequiredForRegenerateRecoveryCodes = $this->evaluate($requiresPassword);

        return $this;
    }

    public function hasEnabledPasskeyAuthentication(): bool
    {
        return $this->enablePasskeyAuthentication;
    }

    public function enablePasskeyAuthentication(Closure | bool $condition = true): static
    {
        $this->enablePasskeyAuthentication = $this->evaluate($condition);

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

    #[\Deprecated('Use enableTwoFactorAuthentication( instead')]
    public function addTwoFactorMenuItem(
        Closure | bool $condition = true,
        ?string $label = null,
        ?string $icon = null
    ): static {
        $this->hasTwoFactorMenuItem = $this->evaluate($condition);

        $this->twoFactorMenuItemLabel = $label;

        $this->twoFactorMenuItemIcon = $icon;

        return $this;
    }

    public function hasTwoFactorMenuItem(): bool
    {
        return $this->hasTwoFactorMenuItem;
    }

    public function getTwoFactorMenuItemLabel(): bool
    {
        return $this->twoFactorMenuItemLabel;
    }

    public function getTwoFactorMenuItemIcon(): bool
    {
        return $this->twoFactorMenuItemIcon;
    }

    public function hasEnabledPasskeyAuthentication(): bool
    {
        return $this->enablePasskeyAuthentication;
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
