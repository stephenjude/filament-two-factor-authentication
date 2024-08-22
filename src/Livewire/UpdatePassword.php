<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePassword extends BaseLivewireComponent
{
    public ?array $data = [];

    public function mount(): void
    {
    }

    public function render()
    {
        return view('auth.update-password');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('currentPassword')
                    ->label(__('Current Password'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->autocomplete('current-password')
                    ->rules([
                        fn() => function (string $attribute, $value, $fail) {
                            if (!Hash::check($value, $this->getUser()->password)) {
                                $fail('The provided password does not match your current password.');
                            }
                        },
                    ]),
                TextInput::make('password')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                    ->password()
                    ->required()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->rule(Password::default())
                    ->autocomplete('new-password')
                    ->dehydrated(fn($state): bool => filled($state))
                    ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                    ->live(debounce: 500)
                    ->same('passwordConfirmation'),
                TextInput::make('passwordConfirmation')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->visible(fn(Get $get): bool => filled($get('password')))
                    ->dehydrated(false),
            ])
            ->statePath('data')
            ->model($this->getUser());
    }

    public function updatePassword(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->sendRateLimitedNotification($exception);

            return;
        }

        $data = Arr::only($this->form->getState(), 'password');

        $user = $this->getUser();

        $user->fill($data);

        if (!$user->isDirty('password')) {
            return;
        }

        $user->save();

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put(['password_hash_'.Filament::getAuthGuard() => $data['password']]);
        }

        $this->data['password'] = null;
        $this->data['currentConfirmation'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->sendDataSavedNotification();
    }

    protected function getFormAction(): Action
    {
        return Action::make('update_password')
            ->label(__('Save'))
            ->submit('updatePassword');
    }
}
