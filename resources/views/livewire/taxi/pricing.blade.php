@section('title',__('Pricing'))
<div>

    <x-baseview title="{{ __('Pricing') }}" :showNew="true">
        <livewire:tables.taxi.pricing-table />
    </x-baseview>

    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('New') }}" action="save" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('New Pricing') }}</p>
            <x-select title="{{ __('Vehicle Type') }}" :options='$vehicleTypes' name="vehicle_type_id" :defer="true" />
            <x-select title="{{ __('Currency') }}" :options='$currencies' name="currency_id" :defer="true" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input title="{{ __('Base Fare') }}" name="base_fare" />
                <x-input title="{{ __('Distance Fare') }}(/km)" name="distance_fare" />
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input title="{{ __('Fare Per Minutes') }}" name="time_fare" />
                <x-input title="{{ __('Minimum Fare') }}" name="min_fare" />
            </div>
            <x-checkbox title="{{ __('Active') }}" name="is_active" :defer="false" />
        </x-modal>
    </div>

    {{-- update form --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Update Pricing') }}</p>
            <x-select title="{{ __('Vehicle Type') }}" :options='$vehicleTypes' name="vehicle_type_id" :defer="true" />
            <x-select title="{{ __('Currency') }}" :options='$currencies' name="currency_id" :defer="true" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input title="{{ __('Base Fare') }}" name="base_fare" />
                <x-input title="{{ __('Distance Fare') }}(/km)" name="distance_fare" />
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input title="{{ __('Fare Per Minutes') }}" name="time_fare" />
                <x-input title="{{ __('Minimum Fare') }}" name="min_fare" />
            </div>
            <x-checkbox title="{{ __('Active') }}" name="is_active" :defer="false" />
        </x-modal>
    </div>

</div>
