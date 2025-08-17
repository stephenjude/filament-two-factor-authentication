<div class="flex justify-center w-full">
    <form method="POST" action="{{ filament()->getCurrentPanel()->getLogoutUrl() }}">
        @csrf
        <x-filament::link tag="button" type="submit" weight="semibold">
            {{__('filament-two-factor-authentication::components.logout.button')}}
        </x-filament::link>
    </form>
</div>
