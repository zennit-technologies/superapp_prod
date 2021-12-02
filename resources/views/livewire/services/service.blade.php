@section('title', __('Services') )
<div>

    <x-baseview title="{{ __('Services') }}" :showNew="true">
        <livewire:tables.service-table />
    </x-baseview>
    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Save') }}" action="save" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Create Service') }}</p>

            <livewire:component.autocomplete-input 
                title="{{ __('Vendor') }}" 
                column="name"
                model="Vendor" 
                errorMessage="{{ $errors->first('vendor_id') }}" 
                :queryClause="$vendorSearchClause" 
                emitFunction="autocompleteVendorSelected"
                :extraQueryData="['service']"
                customQuery="vendor_type" />
                <x-input-error message="{{ $errors->first('vendor_id') }}" />

            <livewire:component.autocomplete-input 
                title="{{ __('Category') }}" 
                column="name"
                model="Category" 
                errorMessage="{{ $errors->first('category_id') }}" 
                emitFunction="autocompleteCategorySelected"
                customQuery="vendor_type_service"
                :extraQueryData="['service']" />
                <x-input-error message="{{ $errors->first('category_id') }}" />

            <x-input title="{{ __('Name') }}" name="name" />
            <x-textarea title="{{ __('Description') }}" name="description" />
            {{-- photos --}}
            <livewire:component.multiple-media-upload 
                title="{{ __('Images') }}" 
                name="photos" 
                types="PNG or JPEG" 
                fileTypes="image/*" 
                emitFunction="photoSelected" />

            <div class="grid grid-cols-2 space-x-2">
                <x-input title="{{ __('Price') }}" name="price" />
                <x-input title="{{ __('Discount Price') }}" name="discount_price" />
            </div>
            <div class="grid grid-cols-2 space-x-2">
                <x-checkbox title="{{ __('Per Hour') }}" name="per_hour" :defer="false" />
                <x-checkbox title="{{ __('Active') }}" name="is_active" :defer="false" />
            </div>

        </x-modal>
    </div>

    {{-- edit service --}}
    <div x-data="{ open: @entangle('showEdit') }" >
        <x-modal confirmText="{{ __('Update') }}" action="update" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Edit Service') }}</p>

            <livewire:component.autocomplete-input 
                title="{{ __('Vendor') }}" 
                column="name"
                model="Vendor" 
                errorMessage="{{ $errors->first('vendor_id') }}" 
                :queryClause="$vendorSearchClause" 
                emitFunction="autocompleteVendorSelected"
                initialEmit="preselectedVendorEmit"
                :extraQueryData="['service']"
                customQuery="vendor_type" />
                <x-input-error message="{{ $errors->first('vendor_id') }}" />

                

            <livewire:component.autocomplete-input 
                title="{{ __('Category') }}" 
                column="name"
                model="Category" 
                errorMessage="{{ $errors->first('category_id') }}" 
                emitFunction="autocompleteCategorySelected"
                initialEmit="preselectedCategoryEmit"
                customQuery="vendor_type_service"
                :extraQueryData="['service']" />
                <x-input-error message="{{ $errors->first('category_id') }}" />

            <x-input title="{{ __('Name') }}" name="name" />
            <x-textarea title="{{ __('Description') }}" name="description" />
            {{-- photos --}}
            <livewire:component.multiple-media-upload 
                title="{{ __('Images') }}" 
                name="photos" 
                types="PNG or JPEG" 
                fileTypes="image/*" 
                emitFunction="photoSelected" />

            <div class="grid grid-cols-2 space-x-2">
                <x-input title="{{ __('Price') }}" name="price" />
                <x-input title="{{ __('Discount Price') }}" name="discount_price" />
            </div>
            <div class="grid grid-cols-2 space-x-2">
                <x-checkbox title="{{ __('Per Hour') }}" name="per_hour" :defer="false" />
                <x-checkbox title="{{ __('Active') }}" name="is_active" :defer="false" />
            </div>

        </x-modal>
    </div>

    {{-- show service details --}}
    <div x-data="{ open: @entangle('showDetails') }">
        <x-modal>
            <p class="text-xl font-semibold">{{ __('Service') }} {{ __('Details') }}</p>
            <x-details.item title="{{ __('Name') }}"
                text="{{ $selectedModel->name ?? '' }}" />
            <x-details.item title="{{ __('Description') }}"
                text="{{ $selectedModel->description ?? '' }}" />
            <div class="grid grid-cols-2 space-x-2">
                <x-details.item title="{{ __('Vendor') }}"
                    text="{{ $selectedModel->vendor->name ?? '' }}" />
                <x-details.item title="{{ __('Category') }}"
                    text="{{ $selectedModel->category->name ?? '' }}" />
            </div>
            <div class="grid grid-cols-2 space-x-2">
                <x-details.item title="{{ __('Price') }}"
                    text="{{ $selectedModel->price ?? '' }}" />
                <x-details.item title="{{ __('Discount') }}"
                    text="{{ $selectedModel->discount_price ?? '' }}" />
            </div>
            <div class="grid grid-cols-2 space-x-2">
                <div>
                    <x-label title="{{ __('Active') }}" />
                    <x-table.bool isTrue="{{ $selectedModel->is_active ?? false }}" />
                </div>
                <div>
                    <x-label title="{{ __('Per Hour') }}" />
                    <x-table.bool isTrue="{{ $selectedModel->per_hour ?? false }}" />
                </div>
                
            </div>
            <x-label title="{{ __('Images') }}" />
            <div class="grid grid-cols-2 space-x-2">
                @foreach (($selectedModel != null ? $selectedModel->getMedia() : []) as $photo)
                    <a href="{{ $photo->getFullUrl() }}" target="_blank">{{ $photo }}</a>
                @endforeach
                
            </div>

        </x-modal>

    </div>
    

</div>
