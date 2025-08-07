<x-filament-panels::page.simple>
    <x-slot name="subheading">
        {{__('filament-two-factor-authentication::pages.subheading').' ' }}
        {{ $this->recoveryAction }}
    </x-slot>

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament::actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <x-filament-two-factor-authentication::logout />

</x-filament-panels::page.simple>
