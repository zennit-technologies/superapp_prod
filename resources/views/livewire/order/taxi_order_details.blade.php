TAXI
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    <x-details.item title="{{ __('Code') }}" text="#{{ $selectedModel->code ?? '' }}" />
    <x-details.item title="{{ __('Status') }}" text="{{ $selectedModel->status ?? '' }}" />
    <x-details.item title="{{ __('Payment Status') }}" text="{{ $selectedModel->payment_status ?? '' }}" />
    <x-details.item title="{{ __('Payment Method') }}" text="{{ $selectedModel->payment_method->name ?? '' }}" />
</div>
<div class="grid grid-cols-1 gap-4 mt-5 border-t md:grid-cols-2 lg:grid-cols-3">
    <x-details.item title="{{ $selectedModel != null && $selectedModel->is_package ? __('Sender')  : __('User') }}" text="{{ $selectedModel->user->name ?? '' }}" />
    <x-details.item title="{{ $selectedModel != null && $selectedModel->is_package ? __('Sender Phone')  : __('User Phone') }}" text="{{ $selectedModel->user->phone ?? '' }}" />

    {{-- Pickup address --}}
    @if($selectedModel != null && !empty($selectedModel->vendor) && !$selectedModel->vendor->vendor_type->is_parcel )
    @if(!empty($selectedModel->delivery_address))
    <x-details.item title="{{ __('Delivery Address') }}" text="{{ $selectedModel->delivery_address->address ?? '' }}" />
    @else
    <x-details.item title="{{ __('Delivery Address') }}" text="{{ __('Customer Self Pickup') }}" />
    @endif

    @endif

</div>

{{-- driver info --}}
<div class="grid grid-cols-1 gap-4 pt-4 mt-4 border-t md:grid-cols-2 lg:grid-cols-3">
    <x-details.item title="{{ __('Driver') }}" text="{{ $selectedModel->driver->name ?? '--' }}" />
    <x-details.item title="{{ __('Driver Phone') }}" text="{{ $selectedModel->driver->phone ?? '--' }}" />
</div>

<div class="grid grid-cols-1 gap-4 mt-5 border-t md:grid-cols-2 ">

    <x-details.item title="{{ __('Pickup Address') }}" text="{{ $selectedModel->taxi_order->pickup_address ?? '' }}" />
    <x-details.item title="{{ __('Dropoff Address') }}" text="{{ $selectedModel->taxi_order->dropoff_address ?? '' }}" />

    <x-details.item title="{{ __('Vehicle Type') }}" text="{{ $selectedModel->taxi_order->vehicle_type->name ?? '' }}" />
</div>
<div class="grid grid-cols-1 gap-4 mt-5 border-t md:grid-cols-2 ">
    <x-details.item title="{{ __('Date of order') }}" text="{{ $selectedModel != null ? $selectedModel->created_at->format('M d, Y \\a\\t H:i a') : '' }}" />
    <x-details.item title="{{ __('Updated At') }}" text="{{ $selectedModel != null ? $selectedModel->updated_at->format('M d, Y \\a\\t H:i a') : '' }}" />
</div>



{{-- order photo --}}
@if($selectedModel != null && $selectedModel->photo && !strpos($selectedModel->photo, 'default.png') )
<p class="mt-8 font-bold text-md">Order Photo</p>
<a href="{{ $selectedModel->photo }}" target="_blank"> <img src="{{ $selectedModel->photo }}" class="w-56 h-56 rounded-sm" /></a>
@endif

{{-- money --}}
<div class="pt-4 border-t justify-items-end">

    <div class="flex items-center justify-start p-4 space-x-20 border-2">
        <p class="my-auto">
            {{-- <x-label title="Driver Tip" /> --}}
            {{ __('Driver Tip') }}
        </p>
        <x-details.p text="{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->tip ?? '0.00' }}" />
    </div>
    <div class="flex items-center justify-end space-x-20 border-b">
        <x-label title="{{ __('Subtotal') }}" />
        <div class="w-6/12 md:w-4/12 lg:w-2/12">
            <x-details.p text="{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->sub_total ?? '' }}" />
        </div>
    </div>
    <div class="flex items-center justify-end space-x-20 border-b">
        <x-label title="{{ __('Discount Amount') }}" />
        <div class="w-6/12 md:w-4/12 lg:w-2/12">
            <x-details.p text="-{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->discount ?? '' }}" />
        </div>
    </div>
    <div class="flex items-center justify-end space-x-20 border-b">
        <x-label title="{{ __('Delivery Fee') }}" />
        <div class="w-6/12 md:w-4/12 lg:w-2/12">
            <x-details.p text="+{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->delivery_fee ?? '' }}" />
        </div>
    </div>
    <div class="flex items-center justify-end space-x-20 border-b">
        <x-label title="{{ __('Tax') }}" />
        <div class="w-6/12 md:w-4/12 lg:w-2/12">
            <x-details.p text="+{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->tax ?? '' }}" />
        </div>
    </div>
    <div class="flex items-center justify-end space-x-20 border-b">
        <x-label title="{{ __('Total') }}" />
        <div class="w-6/12 md:w-4/12 lg:w-2/12">
            <x-details.p text="{{ $selectedModel->taxi_order->currency != null  ? $selectedModel->taxi_order->currency->symbol : setting('currency', '$') }}{{ $selectedModel->total ?? '' }}" />
        </div>
    </div>
</div>
