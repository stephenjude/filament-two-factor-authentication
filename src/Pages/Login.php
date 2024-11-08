<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Pages;

use Arr;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Stephenjude\FilamentTwoFactorAuthentication\Events\TwoFactorAuthenticationChallenged;

class Login extends \Filament\Pages\Auth\Login
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);

            $data = $this->form->getState();

            if (! Filament::auth()->attempt([
                'email' => $data['email'],
                'password' => $data['password'],
            ], $data['remember'] ?? false)) {
                throw $this->getFailureValidationException();
            }

            $user = Filament::auth()->user();

            if (! $user instanceof FilamentUser) {
                Filament::auth()->logout();

                throw $this->getFailureValidationException();
            }

            if (! $user->hasVerifiedEmail() && $user->shouldVerifyEmail()) {
                Filament::auth()->logout();

                return $this->getEmailVerificationPromptResponse();
            }

            if ($user->hasEnabledTwoFactorAuthentication()) {
                Filament::auth()->logout();

                session([
                    'login.id' => $user->getKey(),
                    'login.remember' => $data['remember'] ?? false,
                ]);

                TwoFactorAuthenticationChallenged::dispatch($user);

                return $this->getLoginChallengeResponse();
            }

            return app(LoginResponse::class);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }
    }

    private function validateCredentials(array $data): FilamentUser
    {
        if (! Filament::auth()->validate($this->getCredentialsFromFormData($data))) {
            $this->throwFailureValidationException();
        }

        $model = Filament::auth()->getProvider()->getModel();

        $user = $model::where(Arr::only($data, 'email'))->first();

        if (! ($user instanceof FilamentUser)) {
            $this->throwFailureValidationException();
        }

        if (! $user->canAccessPanel(Filament::getCurrentPanel())) {
            $this->throwFailureValidationException();
        }

        return $user;
    }

    private function getLoginChallengeResponse(): LoginResponse
    {
        return new class implements LoginResponse
        {
            public function toResponse($request)
            {
                return redirect()->to(
                    filament()->getCurrentPanel()->route(
                        'two-factor.challenge'
                    )
                );
            }
        };
    }
}
