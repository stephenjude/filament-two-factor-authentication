<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorChallenge
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = filament()->auth()->user();

        if ($request->is('*/logout') || $request->is('/logout')) {
            return $next($request);
        }

        if ($user?->hasEnabledTwoFactorAuthentication() &&
            ! $user?->isTwoFactorChallengePassed() &&
            ! $user?->passkeyAuthenticated()
        ) {
            return redirect()->to($this->twoFactorChallengeRoute());
        }

        return $next($request);
    }

    protected function twoFactorChallengeRoute(): ?string
    {
        return filament()->getCurrentPanel()?->route('two-factor.challenge');
    }
}
