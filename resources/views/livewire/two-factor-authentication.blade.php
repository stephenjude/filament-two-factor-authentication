<div>
    <x-filament::section :aside="$aside">
        <x-slot name="heading">
            {{__('filament-two-factor-authentication::section.header')}}
        </x-slot>

        <x-slot name="description">
            {{__('filament-two-factor-authentication::section.description')}}
        </x-slot>

        <div class="">
            @if($this->isConfirmingSetup)
                <x-filament-two-factor-authentication::setup-confirmation />
            @elseif($this->enableTwoFactorAuthentication->isVisible())
                <x-filament-two-factor-authentication::enable />
            @elseif($this->disableTwoFactorAuthentication->isVisible())
                <x-filament-two-factor-authentication::enabled />

                @if($this->showRecoveryCodes)
                    <x-filament-two-factor-authentication::recovery-codes />
                @endif

                {{$this->generateNewRecoveryCodes}}

                {{$this->disableTwoFactorAuthentication}}
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</div>
