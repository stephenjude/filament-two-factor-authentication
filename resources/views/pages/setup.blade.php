@php
    use Stephenjude\FilamentTwoFactorAuthentication\Livewire\PasskeyAuthentication;
    use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;
    use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

    $plugin = TwoFactorAuthenticationPlugin::get();

    /** @var \Filament\Models\Contracts\FilamentUser $user */
    $user = filament()->auth()->user();
@endphp
<x-filament-panels::page.simple>

    @if($plugin->hasEnabledTwoFactorAuthentication())
        @livewire(TwoFactorAuthentication::class, ['aside' => false, 'redirectTo' => filament()->getCurrentPanel()->getProfileUrl()])
    @endif

    @if($plugin->hasEnabledPasskeyAuthentication())
        @livewire(PasskeyAuthentication::class, ['aside' => false])
    @endif

    <div class="text-center">
        @if(
            !$plugin->hasForcedTwoFactorSetup() ||
             $user->hasEnabledTwoFactorAuthentication()
        )
            <x-filament::link :href="filament()->getCurrentPanel()->getUrl(filament()->getTenant())"
                              weight="semibold">
                {{__('filament-two-factor-authentication::section.dashboard')}}
            </x-filament::link>
        @else

        @endif

        <x-filament-two-factor-authentication::logout />
    </div>
</x-filament-panels::page.simple>
