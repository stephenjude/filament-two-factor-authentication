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
                <h2 class="text-xl font-medium mb-4">
                    {{__('Finish enabling two factor authentication.')}}
                </h2>

                <p class="text-sm mb-4">
                    {{__("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.")}}
                </p>

                <p class="text-sm font-semibold mb-4">
                    {{__("To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.")}}
                </p>

                <div class="mb-4">
                    {!! $this->getUser()->twoFactorQrCodeSvg() !!}
                </div>

                <form wire:submit="confirmSetup">
                    <div class="mb-4">
                        {{ $this->form }}
                    </div>
                    <div class="flex gap-2">
                        {{$this->confirmSetup}}
                        {{$this->cancelSetup}}
                    </div>
                </form>
            @elseif($this->enableTwoFactorAuthentication->isVisible())
                <h2 class="text-xl font-medium mb-4">
                    {{__('You have not enabled two factor authentication.')}}
                </h2>

                <p class="text-sm mb-4">
                    {{__("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.")}}
                </p>

                {{$this->enableTwoFactorAuthentication}}
            @elseif($this->disableTwoFactorAuthentication->isVisible())
                <h2 class="text-xl font-medium mb-4">{{__('You have enabled two factor authentication.')}}</h2>

                <p class="text-sm mb-4">
                    {{__('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.')}}
                </p>

                <div class="mb-4 bg-gray-100 dark:bg-gray-800 dark:text-gray-200 p-4 rounded-md">
                    @foreach($this->getUser()->recoveryCodes() as $code)
                        <p class="text-sm font-medium mb-2">{{$code}}</p>
                    @endforeach
                </div>

                {{$this->generateNewRecoveryCodes}}

                {{$this->disableTwoFactorAuthentication}}
            @endif
        </div>
    </x-filament::section>

    <x-filament-actions::modals />

    @if(!filament('filament-two-factor-authentication')->hasEnforcedTwoFactorSetup() || filament()->auth()->user()?->hasEnabledTwoFactorAuthentication())
        <div class="my-4 text-center">
            <x-filament::link :href="filament()->getCurrentPanel()->getUrl(filament()->getTenant())" weight="semibold">
                {{__('Dashboard')}}
            </x-filament::link>
        </div>
    @endif
</div>
