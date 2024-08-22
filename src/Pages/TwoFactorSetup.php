<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Pages;

use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class TwoFactorSetup extends  BaseSimplePage{

    protected static string $view = 'filament-two-factor-authentication::pages.two-factor-setup';

    public ?array $data = [];

    public function mount(): void {}

    public function getTitle(): string|Htmlable
    {
        return '';
    }
}
