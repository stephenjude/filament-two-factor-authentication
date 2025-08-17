<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\ConfirmTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\DisableTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\EnableTwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Actions\GenerateNewRecoveryCodes;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

class TwoFactorAuthentication extends Component implements HasActions, HasForms
{
    use Defaults;

    public ?array $data = [];

    public bool $aside = true;

    public ?string $redirectTo = null;

    public bool $showSetupCode = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('filament-two-factor-authentication::livewire.two-factor-authentication');
    }

    public function setupTwoFactorAuthenticationForm(Schema $schema): Schema
    {
        return $schema
            ->live()
            ->statePath('data')
            ->model($this->getUser())
            ->hidden(fn () => ! $this->showSetupCode)
            ->components([
                TextEntry::make('header')
                    ->hiddenLabel()
                    ->state(__('filament-two-factor-authentication::components.setup_confirmation.header')),
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->state(
                        __('filament-two-factor-authentication::components.setup_confirmation.description')
                    ),
                TextEntry::make('notice')
                    ->hiddenLabel()
                    ->state(
                        __('filament-two-factor-authentication::components.setup_confirmation.scan_qr_code')
                    ),
                TextEntry::make('qrcode')
                    ->hiddenLabel()
                    ->state(
                        fn ($record) => $record->two_factor_secret ? new HtmlString($record->twoFactorQrCodeSvg()) : ''
                    ),
                TextEntry::make('setup_key')
                    ->label(fn ($record) => __('filament-two-factor-authentication::components.2fa.setup_key', [
                        'setup_key' => $record->two_factor_secret ? decrypt($record->two_factor_secret) : '',
                    ])),
                TextInput::make('code')
                    ->label(__('filament-two-factor-authentication::components.2fa.code'))
                    ->required(),
                Actions::make([
                    Action::make('confirm')
                        ->label(__('filament-two-factor-authentication::components.2fa.confirm'))
                        ->action(function ($record) {
                            $data = $this->setupTwoFactorAuthenticationForm->getState();

                            app(ConfirmTwoFactorAuthentication::class)($record, $data['code']);

                            $this->showSetupCode = false;
                        }),
                    Action::make('cancel')
                        ->label(__('filament-two-factor-authentication::components.2fa.cancel'))
                        ->outlined()
                        ->action(function ($record) {
                            $this->showSetupCode = false;

                            app(DisableTwoFactorAuthentication::class)($record);
                        }),
                ]),
            ]);
    }

    public function enableTwoFactorAuthenticationForm(Schema $schema): Schema
    {
        return $schema
            ->live()
            ->statePath('data')
            ->model($this->getUser())
            ->hidden(fn ($record) => $record->hasEnabledTwoFactorAuthentication() || $this->showSetupCode)
            ->components([
                TextEntry::make('header')
                    ->hiddenLabel()
                    ->state(__('filament-two-factor-authentication::components.enable.header')),
                TextEntry::make('description')
                    ->hiddenLabel()
                    ->state(__('filament-two-factor-authentication::components.enable.description')),
                Actions::make([
                    Action::make('enableTwoFactorAuthentication')
                        ->modalWidth('md')
                        ->label(__('filament-two-factor-authentication::components.2fa.enable'))
                        ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
                        ->action(function ($record) {
                            $this->showSetupCode = true;

                            app(EnableTwoFactorAuthentication::class)($record);
                        })
                        ->schema(function () {
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
                                                $fail(
                                                    __(
                                                        'filament-two-factor-authentication::components.2fa.wrong_password'
                                                    )
                                                );
                                            }
                                        },
                                    ]),
                            ];
                        }),
                ]),
            ]);
    }

    public function disableTwoFactorAuthenticationForm(Schema $schema): Schema
    {
        return $schema
            ->live()
            ->statePath('data')
            ->model($this->getUser())
            ->hidden(fn () => ! $this->getUser()->hasEnabledTwoFactorAuthentication())
            ->components([
                TextEntry::make('recoveryCode')
                    ->hiddenLabel()
                    ->listWithLineBreaks()
                    ->copyable()
                    ->state(
                        fn () => $this->getUser()->hasEnabledTwoFactorAuthentication()
                            ? $this->getUser()->recoveryCodes()
                            : []
                    ),
                Actions::make([
                    Action::make('generateNewRecoveryCodes')
                        ->label(__('filament-two-factor-authentication::components.2fa.regenerate_recovery_codes'))
                        ->outlined()
                        ->requiresConfirmation(! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword())
                        ->modalWidth('md')
                        ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
                        ->action(fn ($record) => app(GenerateNewRecoveryCodes::class)($record))
                        ->schema(function () {
                            if (! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword()) {
                                return null;
                            }

                            return [
                                TextInput::make('currentPassword')
                                    ->label(__('filament-two-factor-authentication::components.2fa.current_password'))
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->required()
                                    ->autocomplete('current-password')
                                    ->rules([
                                        fn () => function (string $attribute, $value, $fail) {
                                            if (! Hash::check($value, $this->getUser()->password)) {
                                                $fail(
                                                    __(
                                                        'filament-two-factor-authentication::components.2fa.wrong_password'
                                                    )
                                                );
                                            }
                                        },
                                    ]),
                            ];
                        }),
                    Action::make('disableTwoFactorAuthentication')
                        ->label(__('filament-two-factor-authentication::components.2fa.disable'))
                        ->color('danger')
                        ->modalWidth('md')
                        ->modalSubmitActionLabel(__('filament-two-factor-authentication::components.2fa.confirm'))
                        ->action(fn ($record) => app(DisableTwoFactorAuthentication::class)($record))
                        ->requiresConfirmation()
                        ->schema(function () {
                            if (! TwoFactorAuthenticationPlugin::get()->twoFactorSetupRequiresPassword()) {
                                return null;
                            }

                            return [
                                TextInput::make('currentPassword')
                                    ->label(__('filament-two-factor-authentication::components.2fa.current_password'))
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->required()
                                    ->autocomplete('current-password')
                                    ->rules([
                                        fn () => function (string $attribute, $value, $fail) {
                                            if (! Hash::check($value, $this->getUser()->password)) {
                                                $fail(
                                                    __(
                                                        'filament-two-factor-authentication::components.2fa.wrong_password'
                                                    )
                                                );
                                            }
                                        },
                                    ]),
                            ];
                        }),
                ]),
            ]);
    }
}
