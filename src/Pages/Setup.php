<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

class Setup extends BaseSimplePage
{
    protected string $view = 'filament-two-factor-authentication::pages.setup';

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

    public function utilityActionsForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Actions::make([
                    Action::make('dashboard')
                        ->visible(
                            !TwoFactorAuthenticationPlugin::get()->hasForcedTwoFactorSetup()
                            || filament()->auth()->user()->hasEnabledTwoFactorAuthentication()
                        )
                        ->label(__('filament-two-factor-authentication::section.dashboard'))
                        ->url(fn() => \filament()->getCurrentPanel()->getUrl())
                        ->color('gray')
                        ->icon('heroicon-o-home')
                        ->link(),
                ])->fullWidth()
            ]);
    }
}
