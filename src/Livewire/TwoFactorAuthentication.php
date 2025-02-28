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
            ->label(__('Confirm'))
            ->visible(fn () => $this->isConfirmingSetup)
            ->submit('confirmSetup');
    }

    public function cancelSetupAction(): Action
    {
        return Action::make('cancelSetup')
            ->label(__('Cancel'))
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
            ->label(__('Enable'))
            ->visible(
                fn () => ! $this->getUser()->hasEnabledTwoFactorAuthentication()
            )->modalWidth('md')
            ->modalSubmitActionLabel(__('Confirm'))
            ->form([
                TextInput::make('confirmPassword')
                    ->label(__('Confirm Password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete('confirm-password')
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            if (! Hash::check($value, $this->getUser()->password)) {
                                $fail(__('The provided password was incorrect.'));
                            }
                        },
                    ]),
            ])
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
                        'Setup Key: :setup_key',
                        ['setup_key' => decrypt($this->getUser()->two_factor_secret)]
                    )),
                TextInput::make('code')
                    ->label(__('Code'))
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function disableTwoFactorAuthenticationAction(): Action
    {
        return Action::make('disableTwoFactorAuthentication')
            ->label(__('Disable'))
            ->color('danger')
            ->visible(fn () => $this->getUser()->hasEnabledTwoFactorAuthentication())
            ->modalWidth('md')
            ->modalSubmitActionLabel(__('Confirm'))
            ->form([
                TextInput::make('currentPassword')
                    ->label(__('Current Password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete('current-password')
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            if (! Hash::check($value, $this->getUser()->password)) {
                                $fail(__('The provided password was incorrect.'));
                            }
                        },
                    ]),
            ])
            ->action(fn () => app(DisableTwoFactorAuthentication::class)($this->getUser()));
    }

    protected function generateNewRecoveryCodesAction(): Action
    {
        return Action::make('generateNewRecoveryCodes')
            ->label(__('Regenerate Recovery Codes'))
            ->outlined()
            ->visible(fn () => $this->getUser()->hasEnabledTwoFactorAuthentication())
            ->requiresConfirmation()
            ->action(
                function () {
                    $this->showRecoveryCodes = true;
                    app(GenerateNewRecoveryCodes::class)($this->getUser());
                }
            );
    }
}
