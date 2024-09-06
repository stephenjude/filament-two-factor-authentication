<div class="flex justify-center w-full">
    <form method="POST" action="{{ filament()->getCurrentPanel()->getLogoutUrl() }}">
        <x-filament::link tag="button" type="submit" weight="semibold">
            Logout
        </x-filament::link>
    </form>
</div>
