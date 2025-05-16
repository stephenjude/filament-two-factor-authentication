<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\ConfirmTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\DisableTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\EnableTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\GenerateNewRecoveryCodes;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

class TwoFactorAuthentication extends BaseLivewireComponent
{
    public ?array $data = [];

    public bool $aside = true;

    public ?string $redirectTo = null;

    public bool $isConfirmingSetup = false;

    public bool $showRecoveryCodes = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('filament-two-factor-authentication::livewire.two-factor-authentication');
    }

    public function confirmSetup(): void
    {
        try {
            $this->rateLimit(5);

            $data = $this->form->getState();

            app(ConfirmTwoFactorAuthentication::class)($this->getUser(), $data['code']);

            $this->isConfirmingSetup = false;
            $this->showRecoveryCodes = true;

            if ($this->redirectTo) {
                redirect()->to($this->redirectTo);
            }
        } catch (TooManyRequestsException $exception) {
            $this->sendRateLimitedNotification($exception);

            return;
        }
    }

    public function confirmSetupAction(): Action
    {
        return Action::make('confirmSetup')
            ->label(__('filament-two-factor-authentication::components.2fa.confirm'))
            ->visible(fn () => $this->isConfirmingSetup)
            ->submit('confirmSetup');
    }

    public function cancelSetupAction(): Action
    {
        return Action::make('cancelSetup')
            ->label(__('filament-two-factor-authentication::components.2fa.cancel'))
            ->outlined()
            ->visible(fn () => $this->isConfirmingSetup)
            ->action(function () {
                try {
                    $this->rateLimit(5);

                    app(DisableTwoFactorAuthentication::class)($this->getUser());

                    $this->isConfirmingSetup = false;
                } catch (TooManyRequestsException $exception) {
                    $this->sendRateLimitedNotification($exception);

                    return;
                }
            });
    }

    protected function enableTwoFactorAuthenticationAction(): Action
    {
        return Action::make('enableTwoFactorAuthentication')
            ->label(__('filament-two-factor-authentication::components.2fa.enable'))
            ->visible(
                fn () => ! $this->getUser()->hasEnabledTwoFactorAuthentication()
            )->modalWidth('md')
            ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
            ->form(function () {
                if (! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword()) {
                    return null;
                }

                return [
                    TextInput::make('confirmPassword')
                        ->label(__('filament-two-factor-authentication::components.2fa.confirm_password'))
                        ->password()
                        ->revealable(filament()->arePasswordsRevealable())
                        ->required()
                        ->autocomplete('confirm-password')
                        ->rules([
                            fn () => function (string $attribute, $value, $fail) {
                                if (! Hash::check($value, $this->getUser()->password)) {
                                    $fail(__('filament-two-factor-authentication::components.2fa.wrong_password'));
                                }
                            },
                        ]),
                ];
            })
            ->action(function () {
                try {
                    $this->rateLimit(5);

                    app(EnableTwoFactorAuthentication::class)($this->getUser());

                    $this->isConfirmingSetup = true;
                } catch (TooManyRequestsException $exception) {
                    $this->sendRateLimitedNotification($exception);

                    return;
                }
            });
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('setup_key')
                    ->label(fn () => __(
                        'filament-two-factor-authentication::components.2fa.setup_key',
                        ['setup_key' => decrypt($this->getUser()->two_factor_secret)]
                    )),
                TextInput::make('code')
                    ->label(__('filament-two-factor-authentication::components.2fa.code'))
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function disableTwoFactorAuthenticationAction(): Action
    {
        return Action::make('disableTwoFactorAuthentication')
            ->label(__('filament-two-factor-authentication::components.2fa.disable'))
            ->color('danger')
            ->visible(fn () => $this->getUser()->hasEnabledTwoFactorAuthentication())
            ->modalWidth('md')
            ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
            ->form(function () {
                if (! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword()) {
                    return null;
                }

                return [TextInput::make('currentPassword')
                    ->label(__('filament-two-factor-authentication::components.2fa.current_password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete('current-password')
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            if (! Hash::check($value, $this->getUser()->password)) {
                                $fail(__('filament-two-factor-authentication::components.2fa.wrong_password'));
                            }
                        },
                    ]),
                ];
            })
            ->action(fn () => app(DisableTwoFactorAuthentication::class)($this->getUser()));
    }

    protected function generateNewRecoveryCodesAction(): Action
    {
        return Action::make('generateNewRecoveryCodes')
            ->label(__('filament-two-factor-authentication::components.2fa.regenerate_recovery_codes'))
            ->outlined()
            ->visible(fn () => $this->getUser()->hasEnabledTwoFactorAuthentication())

            ->requiresConfirmation(! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword())
            ->modalWidth('md')
            ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
            ->form(function () {
                if (! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword()) {
                    return null;
                }

                return [TextInput::make('currentPassword')
                    ->label(__('filament-two-factor-authentication::components.2fa.current_password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete('current-password')
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            if (! Hash::check($value, $this->getUser()->password)) {
                                $fail(__('filament-two-factor-authentication::components.2fa.wrong_password'));
                            }
                        },
                    ]),
                ];
            })
            ->action(fn () => app(GenerateNewRecoveryCodes::class)($this->getUser()), $this->showRecoveryCodes = true);
    }
}
