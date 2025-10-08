<?php

namespace Mapexss\FilamentTwoFactorAuthentication\Pages;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Mapexss\FilamentTwoFactorAuthentication\Events\ValidTwoFactorRecoveryCodeProvided;

class Recovery extends BaseSimplePage
{
    protected string $view = 'filament-two-factor-authentication::pages.recovery';

    public ?array $data = [];

    public function mount(): void
    {
        if (! Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());

            return;
        }

        $this->form->fill();
    }

    public function authenticate()
    {
        try {
            $this->rateLimit(5);

            $this->form->getState();

            $user = Filament::auth()->user();

            $user->setTwoFactorChallengePassed();

            event(new ValidTwoFactorRecoveryCodeProvided($user));

            return redirect()->intended(filament()->getCurrentOrDefaultPanel()->getUrl());
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }
    }

    public function challengeAction(): Action
    {
        return Action::make('two_factor_challenge_login')
            ->link()
            ->label(__('filament-two-factor-authentication::pages.recovery.action_label'))
            ->url(
                filament()->getCurrentOrDefaultPanel()->route(
                    'two-factor.challenge'
                )
            );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('recovery_code')
                    ->hiddenLabel()
                    ->hint(__('filament-two-factor-authentication::pages.recovery.form_hint'))
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            $user = Filament::auth()->user();

                            $validCode = collect($user->recoveryCodes())->first(
                                fn ($code) => hash_equals($code, $value) ? $code : null
                            );

                            if (! $validCode) {
                                $fail(__('filament-two-factor-authentication::pages.recovery.error'));
                            }
                        },
                    ]),
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-panels::auth/pages/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-two-factor-authentication::pages.recovery.title');
    }
}
