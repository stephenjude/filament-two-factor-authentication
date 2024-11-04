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