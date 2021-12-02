@section('title', __('Vendors') )
<div>

    <x-baseview title="{{ __('Vendors') }}" :showNew="\Auth::user()->hasAnyRole('admin|city-admin')">
        <livewire:tables.vendor-table />
    </x-baseview>

    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal-lg confirmText="{{ __('Save') }}" action="save" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Create Vendor') }}</p>
            <x-input title="{{ __('Name') }}" name="name" />

            {{-- vendor type --}}
            <x-select title="{{ __('Vendor Type') }}" :options='$vendorTypes ?? []' name="vendor_type_id" :defer="false" />
            <x-input title="{{ __('Description') }}" name="description" />


            <div class="grid grid-cols-2 space-x-4">
                <x-input title="{{ __('Phone') }}" name="phone" />
                <x-input title="{{ __('Email') }}" name="email" />
            </div>

            <div class="p-2 mt-4 bg-gray-100 border border-gray-300 rounded">
                <livewire:component.autocomplete-address title="{{ __('Address') }}" name="address" />

                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Latitude') }}" name="latitude" />
                    <x-input title="{{ __('Longitude') }}" name="longitude" />
                </div>
                {{-- delivery zones --}}
                <div class="{{ (!$isPackageVendor) ? 'block' : 'hidden' }} grid items-center grid-cols-1 space-x-4">
                    <x-select title="{{ __('Delivery Zone') }}" :options="$deliveryZones ?? []" name="deliveryZoneID" />
                </div>
            </div>

            {{-- categories --}}
            <x-select2 title="{{ __('Categories') }}" :options="$categories ?? []" name="categoriesIDs" id="categoriesSelect2" :multiple="true" width="100" :ignore="true" />

            {{-- sub-categories --}}
            <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid items-center grid-cols-1 space-x-4">
                <x-checkbox title="{{ __('Has Sub-Categories') }}" name="has_sub_categories" description="{{ __('This will allow products to be attached to sub-categories') }}" :defer="false" />
            </div>

            <hr class="mt-5" />

            <div class="{{ !$isPackageVendor ? 'block' : 'hidden' }}">
                <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Minimum Order Amount') }}" name="min_order" />
                    <x-input title="{{ __('Maximum Order Amount') }}" name="max_order" />
                </div>
                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Base Delivery Fee') }}" name="base_delivery_fee" />
                    <x-input title="{{ __('Delivery Fee') }}" name="delivery_fee" />
                </div>
                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Delivery Range(KM)') }}" name="delivery_range" />
                    <x-checkbox title="{{ __('Charge per KM') }}" name="charge_per_km" description="{{ __('Delivery fee will be per KM') }}" :defer="false" />
                </div>
                <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid items-center grid-cols-2 space-x-4">
                    <x-checkbox title="{{ __('Pickup') }}" name="pickup" description="{{ __('Allows pickup orders') }}" :defer="false" />
                    <x-checkbox title="{{ __('Delivery') }}" name="delivery" description="{{ __('Allows delivery orders') }}" :defer="false" />
                </div>

                <hr class="mt-5" />
            </div>
            <div class="grid items-center grid-cols-2 space-x-4">
                <x-checkbox title="{{ __('Schedule Order') }}" name="allow_schedule_order" description="{{ __('Allows customer to schedule orders') }}" :defer="false" />
                @if(!$isServiceVendor)
                <x-checkbox title="{{ __('Order Auto Assignment') }}" name="auto_assignment" description="{{ __('System will automatic assign order to delivery boy') }}" :defer="false" />
                @endif

                <x-checkbox title="{{ __('Auto Accept Order') }}" name="auto_accept" description="{{ __('System will automatic change pending order to preparing') }}" :defer="false" />
            </div>
            <hr class="mt-4" />
            <div class="grid grid-cols-2 space-x-4">
                <x-checkbox title="{{ __('Use Subscription') }}" name="use_subscription" :defer="false" />
            </div>
            <div class="grid grid-cols-2 space-x-4">
                <x-input title="{{ __('System Commission(%)') }}" name="commission" {{--  disable="{{ $use_subscription ? true:false }}" --}} />
                <x-input title="{{ __('Tax') }}" name="tax" />
            </div>
            <hr class="mt-4" />


            <x-media-upload title="{{ __('Logo') }}" name="photo" :photo="$photo" :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            <x-media-upload title="{{ __('Featured Image') }}" name="secondPhoto" :photo="$secondPhoto" :photoInfo="$secondPhotoInfo" types="PNG or JPEG" rules="image/*" />

            @hasanyrole('city-admin|admin')
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />
            @endhasanyrole

        </x-modal-lg>
    </div>

    {{-- update form --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal-lg confirmText="{{ __('Update') }}" action="update" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Update Vendor') }}</p>
            <x-input title="{{ __('Name') }}" name="name" />

            {{-- vendor type --}}
            <x-select title="{{ __('Vendor Type') }}" :options='$vendorTypes ?? []' name="vendor_type_id" :defer="false" />
            <x-input title="{{ __('Description') }}" name="description" />

            <div class="grid grid-cols-2 space-x-4">
                <x-input title="{{ __('Phone') }}" name="phone" />
                <x-input title="{{ __('Email') }}" name="email" />
            </div>



            <div class="p-2 mt-4 bg-gray-100 border border-gray-300 rounded">
                <livewire:component.autocomplete-address title="{{ __('Address') }}" name="address" address="{{ $address ?? '' }}" />
                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Latitude') }}" name="latitude" />
                    <x-input title="{{ __('Longitude') }}" name="longitude" />
                </div>
                {{-- delivery zones --}}
                <div class="{{ (!$isPackageVendor) ? 'block' : 'hidden' }} grid items-center grid-cols-1 space-x-4">
                    <x-select title="{{ __('Delivery Zone') }}" :options="$deliveryZones ?? []" name="deliveryZoneID" />
                </div>
            </div>

            {{-- categories --}}
            <x-select2 title="{{ __('Categories') }}" :options="$categories ?? []" name="categoriesIDs" id="editCategoriesSelect2" :multiple="true" width="100" :ignore="true" />
            {{-- sub-categories --}}
            <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid items-center grid-cols-1 space-x-4">
                <x-checkbox title="{{ __('Has Sub-Categories') }}" name="has_sub_categories" description="{{ __('This will allow products to be attached to sub-categories') }}" :defer="false" />
            </div>
            <hr class="mt-5" />
            <div class="{{ !$isPackageVendor ? 'block' : 'hidden' }}">
                <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Minimum Order Amount') }}" name="min_order" />
                    <x-input title="{{ __('Maximum Order Amount') }}" name="max_order" />
                </div>
                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Base Delivery Fee') }}" name="base_delivery_fee" />
                    <x-input title="{{ __('Delivery Fee') }}" name="delivery_fee" />
                </div>
                <div class="grid grid-cols-2 space-x-4">
                    <x-input title="{{ __('Delivery Range(KM)') }}" name="delivery_range" />
                    <x-checkbox title="{{ __('Charge per KM') }}" name="charge_per_km" description="{{ __('Delivery fee will be per KM') }}" :defer="false" />
                </div>
                <div class="{{ !$isServiceVendor ? 'block' : 'hidden' }} grid items-center grid-cols-2 space-x-4">
                    <x-checkbox title="{{ __('Pickup') }}" name="pickup" description="{{ __('Allows pickup orders') }}" :defer="false" />
                    <x-checkbox title="{{ __('Delivery') }}" name="delivery" description="{{ __('Allows delivery orders') }}" :defer="false" />
                </div>

                <hr class="mt-5" />
            </div>
            <div class="grid items-center grid-cols-2 space-x-4">
                <x-checkbox title="{{ __('Schedule Order') }}" name="allow_schedule_order" description="{{ __('Allows customer to schedule orders') }}" :defer="false" />
                @if(!$isServiceVendor)
                <x-checkbox title="{{ __('Order Auto Assignment') }}" name="auto_assignment" description="{{ __('System will automatic assign order to delivery boy') }}" :defer="false" />
                @endif

                <x-checkbox title="{{ __('Auto Accept Order') }}" name="auto_accept" description="{{ __('System will automatic change pending order to preparing') }}" :defer="false" />
            </div>
            @role('admin')
            <hr class="mt-4" />
            <div class="grid grid-cols-2 space-x-4">
                <x-checkbox title="{{ __('Use Subscription') }}" name="use_subscription" :defer="false" />
            </div>
            <div class="grid grid-cols-2 space-x-4">
                <x-input title="{{ __('System Commission(%)') }}" name="commission" {{--  disable="{{ $use_subscription ? true:false }}" --}} />
                <x-input title="{{ __('Tax') }}" name="tax" />
            </div>
            <hr class="mt-4" />
            @endrole
            <x-media-upload title="{{ __('Logo') }}" name="photo" preview="{{ $selectedModel->logo ?? '' }}" :photo="$photo" :photoInfo="$photoInfo" types="PNG or JPEG" rules="image/*" />

            <x-media-upload title="{{ __('Featured Image') }}" name="secondPhoto" preview="{{ $selectedModel->feature_image ?? '' }}" :photo="$secondPhoto" :photoInfo="$secondPhotoInfo" types="PNG or JPEG" rules="image/*" />
            @hasanyrole('city-admin|admin')
            <x-checkbox title="{{ __('Active') }}" name="isActive" :defer="false" />
            @endhasanyrole
        </x-modal-lg>
    </div>

    {{-- assign form --}}
    <div x-data="{ open: @entangle('showAssign') }">
        <x-modal confirmText="{{ __('Assign') }}" action="assignManagers" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Assign Managers To Vendor') }}</p>
            <x-select2 title="{{ __('Managers') }}" :options="$managers ?? []" name="managersIDs" id="managersSelect2" :multiple="true" width="100" :ignore="true" />

        </x-modal>
    </div>

    {{-- timing form --}}
    <div x-data="{ open: @entangle('showDayAssignment') }">
        <x-modal-lg confirmText="{{ __('Save') }}" action="saveDays" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Set vendor open/close time') }}</p>
            <div class="flex items-center py-3 mt-10 border-t border-b">
                <div class="w-4/12">{{ __('Day') }}</div>
                <div class="w-4/12">
                    {{ __('Openning Time') }}
                </div>
                <div class="w-4/12 pl-2">
                    {{ __('Closing Time') }}
                </div>
            </div>
            @if(!empty($workingDays))
            @foreach($workingDays as $key => $workingDay)

            <div class="flex items-center pb-3 border-b">
                <div class="w-4/12">{{ $workingDay->name ?? '' }}</div>
                <div class="w-4/12">
                    <x-input title="" type="time" name="dayOpen.{{ $workingDay->pivot->id ?? '' }}" noMargin="true" />
                </div>
                <div class="w-4/12 pl-2">
                    <x-input title="" type="time" name="dayClose.{{ $workingDay->pivot->id ?? '' }}" noMargin="true" />
                </div>
                <div class="flex items-center ml-2 space-x-2">
                    <x-buttons.plain title="{{ __('Delete') }}" wireClick="removeDay('{{ $workingDay->pivot->id ?? '' }}')" bgColor="bg-red-500">
                        <x-heroicon-o-trash class="w-5 h-5" />
                    </x-buttons.plain>
                </div>
            </div>
            @endforeach
            @endif
            <x-buttons.primary title="{{ __('New') }}" type="button" wireClick="$set('showNewDayAssignment',true)" />

        </x-modal-lg>
    </div>

    {{-- new timing form --}}
    <div x-data="{ open: @entangle('showNewDayAssignment') }">
        <x-modal-lg confirmText="{{ __('Save') }}" action="saveNewDay" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Set vendor open/close time') }}</p>
            <div class="flex items-center py-3 mt-10 border-t border-b">
                <div class="w-4/12">{{ __('Day') }}</div>
                <div class="w-4/12">
                    {{ __('Openning Time') }}
                </div>
                <div class="w-4/12 pl-2">
                    {{ __('Closing Time') }}
                </div>
            </div>
            <div class="flex items-center pb-3 space-x-2 border-b">
                <div class="w-4/12">
                    <x-select title="" :options="$days ?? []" name="newSelectedDay" />
                </div>
                <div class="w-4/12">
                    <x-input title="" type="time" name="newDayOpen" noMargin="true" />
                </div>
                <div class="w-4/12 pl-2">
                    <x-input title="" type="time" name="newDayClose" noMargin="true" />
                </div>
            </div>

        </x-modal-lg>
    </div>



    {{-- details modal --}}
    <div x-data="{ open: @entangle('showDetails') }">
        <x-modal-lg>

            <p class="text-xl font-semibold">
                {{ $selectedModel != null ? $selectedModel->name."'s" : '' }}
                Details</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-details.item title="{{ __('Name') }}" text="{{ $selectedModel->name ?? '' }}" />
                <x-details.item title="{{ __('Description') }}" text="{{ $selectedModel->description ?? '' }}" />

                <x-details.item title="{{ __('Phone') }}" text="{{ $selectedModel->phone ?? '' }}" />
                <x-details.item title="{{ __('Email') }}" text="{{ $selectedModel->email ?? '' }}" />

                <x-details.item title="{{ __('Address') }}" text="{{ $selectedModel->address ?? '' }}" />
                <x-details.item title="{{ __('Latitude') }}" text="{{ $selectedModel->latitude ?? '' }}" />
                <x-details.item title="{{ __('Longitude') }}" text="{{ $selectedModel->longitude ?? '' }}" />

                @php
                $categoriesName = $selectedModel != null
                ? implode(
                ', ',
                $selectedModel->categories()->pluck('name')->toArray(),
                )
                : '';
                @endphp
                <x-details.item title="{{ __('Categories') }}" text="">
                    {{ $categoriesName }}
                </x-details.item>
            </div>
            <div class="grid grid-cols-1 gap-4 mt-4 border-t md:grid-cols-2 ">
                <x-details.item title="{{ __('Tax') }}" text="{{ $selectedModel->tax ?? '0' }}%" />
                <x-details.item title="{{ __('Commission') }}" text="{{ $selectedModel->commission ?? '0' }}%" />

            </div>

            @if($selectedModel ? ($selectedModel->vendor_type ? $selectedModel->vendor_type->slug == "parcel": false) : true)
            <div class="grid grid-cols-1 gap-4 mt-4 border-t md:grid-cols-2 ">
                <x-details.item title="{{ __('Delivery Fee') }}" text="{{ $selectedModel->delivery_fee ?? '' }}" />
                <x-details.item title="{{ __('Delivery Range') }}" text="{{ $selectedModel->delivery_range ?? '0' }} KM" />
            </div>
            <div class="grid grid-cols-1 gap-4 pt-4 mt-4 border-t md:grid-cols-2 lg:grid-cols-3">

                <div>
                    <x-label title="{{ __('Status') }}" />
                    <x-table.active :model="$selectedModel" />
                </div>

                <div>
                    <x-label title="{{ __('Available for Pickup') }}" />
                    <x-table.bool isTrue="{{ $selectedModel->pickup ?? false }}" />
                </div>

                <div>
                    <x-label title="{{ __('Available for Delivery') }}" />
                    <x-table.bool isTrue="{{ $selectedModel->delivery ?? false }}" />
                </div>

                <div>
                    <x-label title="{{ __('Open') }}" />
                    <x-table.bool isTrue="{{ $selectedModel->is_open ?? false }}" />
                </div>

            </div>
            @endif

            <hr class="my-4" />
            <p class="font-light">{{ __('Documents') }}</p>
            <div class="grid grid-cols-1 gap-4 pt-4 mt-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($selectedModel->documents ?? [] as $document)
                <img src="{{ $document }}" />
                @endforeach

            </div>

        </x-modal-lg>
    </div>
</div>
