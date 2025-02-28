<h2 class="text-xl font-medium mb-4">
    {{__('filament-two-factor-authentication::components.setup_confirmation.header')}}
</h2>

<p class="text-sm mb-4">
    {{__("filament-two-factor-authentication::components.setup_confirmation.description")}}
</p>

<p class="text-sm font-semibold mb-4">
    {{__("filament-two-factor-authentication::components.setup_confirmation.scan_qr_code")}}
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
