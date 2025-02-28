<h2 class="text-xl font-medium mb-4">{{__('filament-two-factor-authentication::components.recovery_codes.header')}}</h2>

<p class="text-sm mb-4">
    {{__('filament-two-factor-authentication::components.recovery_codes.description')}}
</p>

<div class="mb-4 bg-gray-100 dark:bg-gray-800 dark:text-gray-200 p-4 rounded-md">
    @foreach($this->getUser()->recoveryCodes() as $code)
        <p class="text-sm font-medium mb-2">{{$code}}</p>
    @endforeach
</div>

{{$this->generateNewRecoveryCodes}}

{{$this->disableTwoFactorAuthentication}}
