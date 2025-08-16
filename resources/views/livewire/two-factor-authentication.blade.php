<div>
    <x-filament::section :aside="$aside">
        <x-slot name="heading">
            {{__('filament-two-factor-authentication::section.header')}}
        </x-slot>

        <x-slot name="description">
            {{__('filament-two-factor-authentication::section.description')}}
        </x-slot>

        <div class="fi-sc-form space-y-6">
            {{ $this->setupTwoFactorAuthenticationForm }}

            {{ $this->enableTwoFactorAuthenticationForm }}

            {{ $this->disableTwoFactorAuthenticationForm }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</div>
