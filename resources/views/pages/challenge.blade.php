<x-filament-panels::page.simple>
    <x-slot name="subheading">
        {{__('filament-two-factor-authentication::pages.subheading').' ' }}
        {{ $this->recoveryAction }}
    </x-slot>

    <form id="form" wire:submit="authenticate" class="fi-sc-form space-y-6">
        {{ $this->form }}

        <x-filament::actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </form>

    <x-filament-two-factor-authentication::logout />

</x-filament-panels::page.simple>
