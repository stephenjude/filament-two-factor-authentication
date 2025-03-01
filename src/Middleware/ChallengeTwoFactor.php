<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChallengeTwoFactor
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = filament()->auth()->user();

        if ($request->is('*/logout') || $request->is('/logout')) {
            return $next($request);
        }

        if ($user?->hasEnabledTwoFactorAuthentication() && ! $user?->isTwoFactorChallengePassed()) {
            return redirect()->to($this->redirectTo());
        }

        return $next($request);

    }

    protected function redirectTo(): ?string
    {
        return filament()->getCurrentPanel()?->route(
            'two-factor.challenge'
        );
    }
}
