<div class="flex justify-center w-full">
    @include('filament-two-factor-authentication::components.partials.passkey-authenticate-script')

    <form id="passkey-login-form" method="POST" action="{{ filament()->getCurrentPanel()->route('passkeys.login') }}">
        @csrf
    </form>

    <div class="flex w-full">
        @if($message = session()->get('authenticatePasskey::message'))
            <div class="bg-red-100 text-red-700 p-4 border border-red-400 rounded">
                {{ $message }}
            </div>
        @endif
    </div>

    <div style="text-align: center" onclick="authenticateWithPasskey()">
        <x-filament::link href="#" weight="normal" :class="'text-center'" icon="heroicon-o-finger-print">
            {{__("filament-two-factor-authentication::components.passkey.login")}}
        </x-filament::link>
    </div>
</div>

