<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Livewire;

use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use JetBrains\PhpStorm\Deprecated;
use Livewire\Component;

#[Deprecated]
abstract class BaseLivewireComponent extends Component implements HasActions, HasForms
{
    use Defaults;
}
