@php
    use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

    $plugin = TwoFactorAuthenticationPlugin::get();

    /** @var \Filament\Models\Contracts\FilamentUser $user */
    $user = filament()->auth()->user();
     $panel = filament()->getCurrentPanel();
@endphp
<x-filament-panels::page.simple>

    @if($plugin->hasEnabledTwoFactorAuthentication())
        @livewire(TwoFactorAuthentication::class, ['aside' => false, 'redirectTo' => filament()->getCurrentPanel()->getProfileUrl()])
    @endif

    @if($plugin->hasEnabledPasskeyAuthentication())
        @livewire(PasskeyAuthentication::class, ['aside' => false])
    @endif

    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-4">
        @if(!$plugin->hasForcedTwoFactorSetup() || $user->hasEnabledTwoFactorAuthentication())
            <x-filament::button
                tag="a"
                href="{{ $panel->getUrl() }}"
                icon="heroicon-o-arrow-left"
                color="gray"
                size="sm"
                class="w-full sm:w-auto"
            >
                {{ __('filament-two-factor-authentication::section.dashboard') }}
            </x-filament::button>
        @endif

        <x-filament-two-factor-authentication::logout
            class="w-full sm:w-auto"
            icon="heroicon-o-arrow-right-start-on-rectangle"
            size="sm"
        />
    </div>
</x-filament-panels::page.simple>
