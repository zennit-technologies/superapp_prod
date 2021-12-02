@section('title',  __('Banners') )
<div>

    <x-baseview title="{{ __('Banners') }}">
        <livewire:tables.banner-table />
    </x-baseview>

    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Save') }}" action="save">
            <p class="text-xl font-semibold">{{ __('Create Banner') }}</p>
            <x-media-upload title="{{ __('Photo') }}" name="photo" :photo="$photo"
                :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            {{-- orders --}}
            <div class="p-2 my-2 border border-red-500 border-dashed rounded">
                <p><span class="font-bold text-red-500">Note:</span> {{ __("In order of arrangement") }}.</p>
                <p class="">{{ __("Link - When provided") }}</p>
                <p class="">{{ __("Vendor - when provied and link is empty") }}</p>
                <p class="">{{ __("Category - when link & vendor is empty") }}</p>
            </div>

            {{-- link --}}
            <x-input title="{{ __('External Link') }}" name="link" placeholder="" />
            {{-- vendors --}}
            <livewire:component.autocomplete-input 
                title="{{ __('Vendor') }}" 
                column="name"
                model="Vendor" 
                :queryClause="$vendorSearchClause" 
                emitFunction="autocompleteVendorSelected"
                onclearCalled="clearAutocompleteFieldsEvent"
            />
            {{-- category --}}
            <x-select title="{{ __('Category') }}" :options='$categories' name="category_id" :defer="true" />
            
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />

        </x-modal>
    </div>

    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update">

            <p class="text-xl font-semibold">{{ __('Edit Banner') }}</p>
            <x-media-upload title="{{ __('Photo') }}" name="photo"
                preview="{{ $selectedModel->photo ?? '' }}" :photo="$photo"
                :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            {{-- orders --}}
            <div class="p-2 my-2 border border-red-500 border-dashed rounded">
                <p><span class="font-bold text-red-500">Note:</span> {{ __("In order of arrangement") }}.</p>
                <p class="">{{ __("Link - When provided") }}</p>
                <p class="">{{ __("Vendor - when provied and link is empty") }}</p>
                <p class="">{{ __("Category - when link & vendor is empty") }}</p>
            </div>
                
            {{-- link --}}
            <x-input title="{{ __('External Link') }}" name="link" placeholder="" />
            {{-- vendors --}}
            <livewire:component.autocomplete-input 
                title="{{ __('Vendor') }}"
                column="name"
                model="Vendor"
                initialEmit="preselectedVendorEmit"
                emitFunction="autocompleteVendorSelected"
                onclearCalled="clearAutocompleteFieldsEvent"
            />
            {{-- category --}}
            <x-select title="{{ __('Category') }}" :options='$categories' name="category_id"
                selected="{{ !empty($selectedModel) ? $selectedModel->category_id : '' }}"
                :defer="true" />
            
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />


        </x-modal>
    </div>
</div>
