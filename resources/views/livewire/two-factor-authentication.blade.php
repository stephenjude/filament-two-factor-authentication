<div>
    <x-filament::section :aside="$aside">
        <x-slot name="heading">
            {{__('Two Factor Authentication')}}
        </x-slot>

        <x-slot name="description">
            {{__('Add additional security to your account using two factor authentication.')}}
        </x-slot>

        <div class="">
            @if($this->isConfirmingSetup)
                <x-filament-two-factor-authentication::setup-confirmation />
            @elseif($this->enableTwoFactorAuthentication->isVisible())
                <x-filament-two-factor-authentication::enable />
            @elseif($this->disableTwoFactorAuthentication->isVisible())
                <x-filament-two-factor-authentication::recovery-codes />
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />

    @if(str(url()->current())->contains('two-factor-setup'))
        @if(!filament('filament-two-factor-authentication')->hasEnforcedTwoFactorSetup() || filament()->auth()->user()?->hasEnabledTwoFactorAuthentication())
            <div class="my-4 text-center">
                <x-filament::link :href="filament()->getCurrentPanel()->getUrl(filament()->getTenant())"
                                  weight="semibold">
                    {{__('Dashboard')}}
                </x-filament::link>
            </div>
        @endif

        @if($this->enableTwoFactorAuthentication->isVisible())
            <div class="my-4 text-center">
                <x-filament-two-factor-authentication::logout />
            </div>
        @endif
    @endif
</div>
