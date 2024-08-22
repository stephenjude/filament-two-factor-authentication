<div>
    <x-filament::section aside>
        <x-slot name="heading">
            {{__('Profile Information')}}
        </x-slot>

        <x-slot name="description">
            {{__('Update your account\'s profile information and email address.')}}
        </x-slot>


        <form wire:submit="updateProfile">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                {{$this->getFormAction()}}
            </div>
        </form>

    </x-filament::section>

    <x-filament-actions::modals/>
</div>
