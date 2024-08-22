<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Arr;

class UpdateProfile extends BaseLivewireComponent
{
    public ?array $data = [];

    public function mount(): void
    {
        $data = $this->getUser()->only(['name', 'email']);

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                    ->email()
                    ->required()
                    ->rules(['email:filter'])
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ])
            ->statePath('data')
            ->model($this->getUser());
    }

    public function updateProfile(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->sendRateLimitedNotification($exception);

            return;
        }

        $data = Arr::only($this->form->getState(), 'name');

        $user = $this->getUser();

        $user->fill($data);

        if ($user->isDirty('name')) {
            return;
        }

        $user->save();

        $this->sendDataSavedNotification();
    }

    public function render()
    {
        return view('filament-two-factor-authentication::livewire.update-profile');
    }

    protected function getFormAction(): Action
    {
        return Action::make('update_profile')
            ->label(__('Save'))
            ->submit('updateProfile');
    }
}
