<div class="flex justify-center w-full" style="text-align: center">
    @include('filament-two-factor-authentication::components.partials.passkey-authenticate-script')

    <form id="passkey-login-form" method="POST" action="{{ filament()->getCurrentOrDefaultPanel()->route('passkeys.login') }}">
        @csrf
    </form>


    <div>
        @if($message = session()->get('authenticatePasskey::message'))
            <x-filament::link tag="a" weight="light"  color="danger">{{ __('filament-two-factor-authentication::components.passkey.error', ['message' => $message]) }}</x-filament::link>
            <br><br>
        @endif
    </div>

    <div onclick="authenticateWithPasskey()">
        <x-filament::link
            href="#"
            weight="normal"
            :tooltip='__("filament-two-factor-authentication::components.passkey.tooltip")'
            icon="heroicon-o-finger-print">
            {{__("filament-two-factor-authentication::components.passkey.login")}}
        </x-filament::link>
    </div>
</div>

