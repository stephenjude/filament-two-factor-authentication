@php use Stephenjude\FilamentTwoFactorAuthentication\Livewire\{TwoFactorAuthentication,UpdatePassword,UpdateProfile}; @endphp
<x-filament-panels::page>
    @livewire(UpdateProfile::class)
    @livewire(UpdatePassword::class)
    @livewire(TwoFactorAuthentication::class)
</x-filament-panels::page>
