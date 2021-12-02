@section('title',  __('Menu') )
<div>

    <x-baseview title="{{ __('Menu') }}">
        <livewire:tables.menu-table />
    </x-baseview>

    {{--  new form  --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Save') }}" action="save">
            <p class="text-xl font-semibold">{{ __('Create Menu') }}</p>

            <x-input title="{{ __('Name') }}" name="name" />
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />

        </x-modal>
    </div>

    {{--  update form  --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update">
            <p class="text-xl font-semibold">{{ __('Update Menu') }}</p>

            <x-input title="{{ __('Name') }}" name="name" />
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />

        </x-modal>
    </div>

</div>

