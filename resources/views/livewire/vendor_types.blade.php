@section('title', __('Vendor Types') )
<div>

    <x-baseview title="{{ __('Vendor Types') }}">
        <livewire:tables.vendor-type-table />
    </x-baseview>

    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal-lg confirmText="{{ __('New') }}" action="save" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('New Vendor Type') }}</p>
            <x-select title="{{ __('Type') }}" :options='$types' name="slug" :defer="false" />
            <x-input title="{{ __('Name') }}" name="name" />
            <x-input title="Color" name="color" type="color" class="h-10" />
            <x-input title="{{ __('Description') }}" name="description" />

          
            <x-media-upload title="{{ __('Logo') }}" name="photo"
                preview="{{ $selectedModel->logo ?? '' }}" :photo="$photo"
                :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />

        </x-modal-lg>
    </div>
    {{-- update form --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal-lg confirmText="{{ __('Update') }}" action="update" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Update Vendor Type') }}</p>
            <x-input title="{{ __('Name') }}" name="name" />
            <x-input title="Color" name="color" type="color" class="h-10" />
            <x-input title="{{ __('Description') }}" name="description" />

          
            <x-media-upload title="{{ __('Logo') }}" name="photo"
                preview="{{ $selectedModel->logo ?? '' }}" :photo="$photo"
                :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />

        </x-modal-lg>
    </div>


</div>
