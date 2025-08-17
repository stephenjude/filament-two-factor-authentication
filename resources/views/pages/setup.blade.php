@php
    use Stephenjude\FilamentTwoFactorAuthentication\Livewire\PasskeyAuthentication;
    use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;
    use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

    $plugin = TwoFactorAuthenticationPlugin::get();
@endphp
<x-filament-panels::page.simple>

    @if($plugin->hasEnabledTwoFactorAuthentication())
        @livewire(TwoFactorAuthentication::class, ['aside' => false])
    @endif

    @if($plugin->hasEnabledPasskeyAuthentication())
        @livewire(PasskeyAuthentication::class, ['aside' => false])
    @endif

    {{$this->utilityActionsForm}}
</x-filament-panels::page.simple>
