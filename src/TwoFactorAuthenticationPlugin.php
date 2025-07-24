<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use JetBrains\PhpStorm\Deprecated;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\ForceTwoFactorSetup;
use Stephenjude\FilamentTwoFactorAuthentication\Middleware\TwoFactorChallenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;

class TwoFactorAuthenticationPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool $enablePasskeyAuthentication = false;

    protected bool $enableTwoFactorAuthentication = false;

    #[Deprecated('Use the `hasForcedTwoFactorSetup` property instead.')]
    protected bool $hasEnforcedTwoFactorSetup = false;

    protected bool $hasForcedTwoFactorSetup = false;

    protected bool $twoFactorSetupRequiresPassword = false;

    protected string $enforceTwoFactorSetupMiddleware = ForceTwoFactorSetup::class;

    protected string | bool $twoFactorChallengeMiddleware = TwoFactorChallenge::class;

    protected bool $hasTwoFactorMenuItem = false;

    protected ?string $twoFactorMenuItemLabel = 'filament-two-factor-authentication::plugin.user_menu_item_label';

    protected ?string $twoFactorMenuItemIcon = 'heroicon-o-lock-closed';

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
        if (! $this->hasEnabledTwoFactorAuthentication() && ! $this->hasEnabledPasskeyAuthentication()) {
            return;
        }

        if ($this->hasEnabledPasskeyAuthentication()) {
            $this->registerPasskeyAuthenticationHook($panel);
        }

        $panel
            ->routes(fn () => [
                Route::get('/two-factor-challenge', Challenge::class)->name('two-factor.challenge'),
                Route::get('/two-factor-recovery', Recovery::class)->name('two-factor.recovery'),
                Route::get('/two-factor-setup', Setup::class)->name('two-factor.setup'),
                Route::prefix('passkeys')->group(function () {
                    Route::get('authentication-options', GeneratePasskeyAuthenticationOptionsController::class)
                        ->name('passkeys.authentication_options');
                    Route::post('authenticate', AuthenticateUsingPasskeyController::class)
                        ->name('passkeys.login');
                }),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->visible($this->hasTwoFactorMenuItem())
                    ->url(fn (): string => $panel->route('two-factor.setup'))
                    ->label(fn () => __($this->getTwoFactorMenuItemLabel()))
                    ->icon(fn () => $this->getTwoFactorMenuItemIcon()),
            ])
            ->authMiddleware(
                array_filter([
                    $this->getTwoFactorChallengeMiddleware(),
                    $this->hasForcedTwoFactorSetup() ? $this->getForcedTwoFactorSetupMiddleware() : null,
                ])
            );
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
    public function setChallengeTwoFactorMiddleware(Closure | string | bool $middleware = TwoFactorChallenge::class): static
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

    #[Deprecated('Use forceTwoFactorSetup() instead')]
    public function enforceTwoFactorSetup(
        Closure | bool $condition = true,
        Closure | string $middleware = ForceTwoFactorSetup::class
    ): static {
        $this->hasForcedTwoFactorSetup = $this->evaluate($condition);

        $this->enforceTwoFactorSetupMiddleware = $this->evaluate($middleware);

        $this->hasEnforcedTwoFactorSetup = $this->evaluate($condition);

        return $this;
    }

    public function forceTwoFactorSetup(
        Closure | bool $condition = true,
        Closure | bool $requiresPassword = true,
        Closure | string $middleware = ForceTwoFactorSetup::class,
    ): static {
        $this->hasForcedTwoFactorSetup = $this->evaluate($condition);

        $this->enforceTwoFactorSetupMiddleware = $this->evaluate($middleware);

        $this->twoFactorSetupRequiresPassword = $this->evaluate($requiresPassword);

        $this->hasEnforcedTwoFactorSetup = $this->evaluate($condition);

        $this->isPasswordRequiredForEnable = $this->evaluate($requiresPassword);

        $this->isPasswordRequiredForDisable = $this->evaluate($requiresPassword);

        $this->isPasswordRequiredForRegenerateRecoveryCodes = $this->evaluate($requiresPassword);

        return $this;
    }

    public function enableTwoFactorAuthentication(
        Closure | bool $condition = true,
        Closure | string $challengeMiddleware = TwoFactorChallenge::class,
    ): static {
        $this->enableTwoFactorAuthentication = $this->evaluate($condition);

        $this->twoFactorChallengeMiddleware = $this->evaluate($challengeMiddleware);

        return $this;
    }

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return $this->enableTwoFactorAuthentication;
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

    public function registerPasskeyAuthenticationHook(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): string => Blade::render('<x-filament-two-factor-authentication::passkey-login />'),
        );
    }

    #[Deprecated('Use getForcedTwoFactorSetupMiddleware() instead')]
    public function getEnforceTwoFactorSetupMiddleware(): string
    {
        return $this->enforceTwoFactorSetupMiddleware;
    }

    public function getForcedTwoFactorSetupMiddleware(): string
    {
        return $this->enforceTwoFactorSetupMiddleware;
    }

    #[Deprecated('Use hasForcedTwoFactorSetup() instead')]
    public function hasEnforcedTwoFactorSetup(): bool
    {
        return $this->hasForcedTwoFactorSetup;
    }

    public function hasForcedTwoFactorSetup(): bool
    {
        return $this->hasForcedTwoFactorSetup;
    }

    public function addTwoFactorMenuItem(
        Closure | bool $condition = true,
        Closure | string | null $label = null,
        Closure | string | null $icon = null,
    ): static {
        $this->hasTwoFactorMenuItem = $this->evaluate($condition);

        $this->twoFactorMenuItemLabel = $this->evaluate($label) ?? $this->twoFactorMenuItemLabel;

        $this->twoFactorMenuItemIcon = $this->evaluate($icon) ?? $this->twoFactorMenuItemIcon;

        return $this;
    }

    public function hasTwoFactorMenuItem(): bool
    {
        return $this->hasTwoFactorMenuItem;
    }

    public function getTwoFactorMenuItemLabel(): ?string
    {
        return $this->twoFactorMenuItemLabel;
    }

    public function getTwoFactorMenuItemIcon(): ?string
    {
        return $this->twoFactorMenuItemIcon;
    }

    public function boot(Panel $panel): void
    {
        Event::listen(function (PasskeyUsedToAuthenticateEvent $event) {
            Cache::remember(
                "passkey::auth::{$event->passkey->authenticatable->id}",
                now()->addMinutes(3),
                fn () => true
            );
        });
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
}
