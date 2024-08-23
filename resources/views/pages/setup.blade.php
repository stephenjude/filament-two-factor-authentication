@php use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication; @endphp
<x-filament-panels::page.simple>
    @livewire(TwoFactorAuthentication::class, ['aside' => false, 'redirectTo' => filament()->getCurrentPanel()->getProfileUrl()])

    @if(!filament('filament-two-factor-authentication')->hasEnforcedTwoFactorSetup() || filament()->auth()->user()?->hasEnabledTwoFactorAuthentication())
        <x-filament::link :href="filament()->getCurrentPanel()->getUrl(filament()->getTenant())" weight="semibold">
            Dashboard
        </x-filament::link>
    @endif
</x-filament-panels::page.simple>
