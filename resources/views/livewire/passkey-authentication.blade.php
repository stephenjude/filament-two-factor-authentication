<div>
    <x-filament::section :aside="$aside">
        <x-slot name="heading">
            {{__('filament-two-factor-authentication::section.passkey.header')}}
        </x-slot>

        <x-slot name="description">
            {{__('filament-two-factor-authentication::section.passkey.description')}}
        </x-slot>

        <div class="fi-sc-form space-y-6">
            {{ $this->form }}

            {{ $this->table }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</div>

@include('passkeys::livewire.partials.createScript')
