<?php

namespace Mapexss\FilamentTwoFactorAuthentication\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Mapexss\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

class PasskeyAuthentication extends PasskeysComponent implements HasActions, HasForms, HasTable
{
    use Defaults;
    use InteractsWithTable;

    public bool $aside = true;

    public bool $showSetupForm = false;

    public function render(): View
    {
        return view('filament-two-factor-authentication::livewire.passkey-authentication');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->getUser()->passkeys()->latest())
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label(__('Name'))
                        ->description(
                            fn ($record) => $record->last_used_at
                                ? $record->last_used_at->diffForHumans()
                                : __('Never used')
                        ),
                ]),
            ])
            ->recordActions([
                DeleteAction::make()
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
                                        if (! \Hash::check($value, $this->getUser()->password)) {
                                            $fail(
                                                __('filament-two-factor-authentication::components.2fa.wrong_password')
                                            );
                                        }
                                    },
                                ]),
                        ];
                    }),
            ])
            ->paginated(false);
    }

    public function createPasskeyForm(Schema $schema): Schema
    {
        return $schema
            ->live()
            ->statePath('data')
            ->model($this->getUser())
            ->components([
                TextEntry::make('header')
                    ->hiddenLabel()
                    ->state(__('filament-two-factor-authentication::components.passkey.notice.header')),
                Actions::make([
                    Action::make('addPasskey')
                        ->label(__('filament-two-factor-authentication::components.passkey.add'))
                        ->modalWidth(Width::Medium)
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament-two-factor-authentication::components.passkey.name'))
                                ->required()
                                ->autocomplete(false),
                        ])
                        ->action(function ($data) {

                            $this->name = $data['name'];

                            $this->dispatch('passkeyPropertiesValidated', [
                                'passkeyOptions' => json_decode($this->generatePasskeyOptions()),
                            ]);
                        }),
                ]),
            ]);
    }

    public function storePasskey(string $passkey): void
    {
        parent::storePasskey($passkey);

        Notification::make()
            ->title(__('filament-two-factor-authentication::components.passkey.added'))
            ->success()
            ->send();
    }
}
