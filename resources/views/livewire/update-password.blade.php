<div>
    <x-filament::section aside>
        <x-slot name="heading">
            {{__('Update Password')}}
        </x-slot>

        <x-slot name="description">
            {{__('Ensure your account is using a long, random password to stay secure.')}}
        </x-slot>


        <form wire:submit="updatePassword">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                {{$this->getFormAction()}}
            </div>
        </form>

    </x-filament::section>


    <x-filament-actions::modals/>
</div>
