<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Pages;

use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;

class Setup extends BaseSimplePage
{

    protected static string $view = 'filament-two-factor-authentication::pages.setup';

    public ?array $data = [];

    public function mount(): void
    {
        if (!Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
