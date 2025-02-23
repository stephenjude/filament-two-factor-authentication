<h2 class="text-xl font-medium mb-4">
    {{__('Finish enabling two factor authentication.')}}
</h2>

<p class="text-sm mb-4">
    {{__("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.")}}
</p>

<p class="text-sm font-semibold mb-4">
    {{__("To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.")}}
</p>

<div class="mb-4 p-2 bg-white inline-block rounded-lg">
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
