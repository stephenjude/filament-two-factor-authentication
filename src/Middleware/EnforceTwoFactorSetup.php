<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceTwoFactorSetup
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = filament()->auth()->user();

        if ($request->is('*/logout') || $request->is('logout') || $request->is('*/profile')) {
            return $next($request);
        }

        if (! $user?->hasEnabledTwoFactorAuthentication()) {
            return redirect()->to($this->redirectTo());
        }

        return $next($request);
    }

    protected function redirectTo(): ?string
    {
        return filament()->getCurrentPanel()?->route(
            'two-factor.setup'
        );
    }
}
