<form
    style="display: flex; justify-content: center; align-items: center;"
    method="POST"
    action="{{ filament()->getCurrentOrDefaultPanel()->getLogoutUrl() }}">
    @csrf
    <x-filament::link tag="button" type="submit" weight="semibold">
        {{__('filament-two-factor-authentication::components.logout.button')}}
    </x-filament::link>
</form>
