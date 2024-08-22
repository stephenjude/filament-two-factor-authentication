@php use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication; @endphp
<x-filament-panels::page.simple>
    @livewire(TwoFactorAuthentication::class, ['aside' => false, 'redirectTo' => filament()->getCurrentPanel()->getProfileUrl()])
</x-filament-panels::page.simple>
