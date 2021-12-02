@section('title', 'Orders')
<div>

    <x-baseview title="{{ __('Orders') }}" :showNew="true">
        @if( $stopRefresh)
        <div>
            @else
            <div wire:poll.20000ms="refreshDataTable">
                @endif
                <livewire:tables.order-table />
            </div>
    </x-baseview>

    {{-- details moal --}}
    <div x-data="{ open: @entangle('showDetails') }">
        <x-modal-lg>
            <p class="text-xl font-semibold">{{ __('Order Details') }}</p>
            @if (!empty($selectedModel) )
                @switch($selectedModel->order_type)
                    @case("package")
                        @include('livewire.order.package_order_details')
                        @break
                    @case("parcel")
                        @include('livewire.order.package_order_details')
                        @break
                    @case("service")
                        @include('livewire.order.service_order_details')
                        @break
                    @case("taxi")
                        @include('livewire.order.taxi_order_details')
                        @break

                    @default
                        @include('livewire.order.regular_order_details')
                        @break
                @endswitch
            @endif
        </x-modal-lg>
    </div>

    {{-- edit modal --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update">

            <p class="text-xl font-semibold">{{ __('Edit Order') }}</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-details.item title="{{ __('Code') }}" text="#{{ $selectedModel->code ?? '' }}" />
                <x-details.item title="{{ __('Status') }}" text="{{ $selectedModel->status ?? '' }}" />
                <x-details.item title="{{ __('Payment Status') }}" text="{{ $selectedModel->payment_status ?? '' }}" />
                <x-details.item title="{{ __('Payment Method') }}" text="{{ $selectedModel->payment_method->name ?? '' }}" />
            </div>
            @if ($selectedModel->is_food ?? false)
            <div class="gap-4 mt-5 border-t">
                <p class="w-full py-4 text-center underline cursor-pointer text-primary-500" wire:click="showEditOrderProducts">{{ __("Edit Products") }}</p>
            </div>
            @endif
            <div class="gap-4 mt-5 border-t">
                {{-- with initial emit --}}
                {{-- delivery boy --}}
                <livewire:component.autocomplete-input title="{{ __('Delivery Boy') }}" placeholder="{{ __('Search for driver') }}" column="name" model="User" customQuery="driver" initialEmit="preselectedDeliveryBoyEmit" emitFunction="autocompleteDriverSelected" onclearCalled="clearAutocompleteFieldsEvent" />
                {{-- <x-select title="{{ __('Delivery Boy') }}" :options="$deliveryBoys ?? []"
                name="deliveryBoyId" :noPreSelect="true" /> --}}
                <x-select title="{{ __('Status') }}" :options="$orderStatus ?? []" name="status" />
                <x-select title="{{ __('Payment Status') }}" :options="$orderPaymentStatus ?? []" name="paymentStatus" />
                <x-input title="{{ __('Note') }}" name="note" />

            </div>
        </x-modal>
    </div>


    {{-- edit order products modal --}}
    @livewire('edit-order-livewire')


    {{-- payment review moal --}}
    <div x-data="{ open: @entangle('showAssign') }">
        <x-modal confirmText="{{ __('Approve') }}" action="approvePayment">

            <p class="text-xl font-semibold">{{ __('Order Payment Proof') }}</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-details.item title="{{ __('Transaction Code') }}" text="{{ $selectedModel->payment->ref ?? '' }}" />
                <x-details.item title="{{ __('Status') }}" text="{{ $selectedModel->payment->status ?? '' }}" />
                <x-details.item title="{{ __('Payment Method') }}" text="{{ $selectedModel->payment_method->name ?? '' }}" />
                <div>
                    <x-label title="{{ __('Transaction Photo') }}" />
                    <a href="{{ $selectedModel->payment->photo ?? '' }}" target="_blank">
                        <img src="{{ $selectedModel->payment->photo ?? '' }}" class="w-32 h-32" />
                    </a>
                </div>
            </div>
        </x-modal>
    </div>







    {{-- New order placement --}}
    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal-lg confirmText="{{ __('Next') }}" action="showOrderSummary" :clickAway="false">
            <p class="text-xl font-semibold">{{ __('Create Order') }}</p>

            <livewire:component.autocomplete-input title="{{ __('Vendor') }}" column="name" model="Vendor" :queryClause="$vendorSearchClause" emitFunction="autocompleteVendorSelected" onclearCalled="clearAutocompleteFieldsEvent" />

            <livewire:component.autocomplete-input title="{{ __('Product') }}" column="name" model="Product" emitFunction="autocompleteProductSelected" updateQueryClauseName="productQueryClasueUpdate" :clear="true" :queryClause="$productSearchClause" onclearCalled="clearAutocompleteFieldsEvent" />

            <p class="mt-4 font-medium text-md">{{ __('Products') }}</p>
            <table class="w-full p-2 border rounded-sm">
                <thead>
                    <tr>
                        <th class="w-1/12 p-2 bg-gray-300 border-b">S/N</th>
                        <th class="w-3/12 p-2 bg-gray-300 border-b">Name</th>
                        <th class="w-4/12 p-2 bg-gray-300 border-b">Options</th>
                        <th class="w-2/12 p-2 bg-gray-300 border-b">QTY</th>
                        <th class="w-2/12 p-2 bg-gray-300 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newOrderProducts ?? [] as $key => $product)
                    <tr>
                        <td class="items-center w-1/12 px-2 py-1 border-b">
                            <span>{{ $key + 1 }}</span>
                        </td>
                        <td class="items-center w-2/12 px-2 py-1 border-b">
                            {{ $product->name }}
                        </td>
                        <td class="items-center w-4/12 px-2 py-1 border-b">
                            @foreach($product->option_groups as $option_group)
                            <div class="p-1 mb-2 border-b">
                                <p class="text-sm font-medium">{{ $option_group->name }}</p>
                                <p class="ml-2">
                                    @foreach($option_group->options as $option)

                                    @php
                                    if($option_group->multiple){
                                    $optionId = "newProductOptions.".$product->id.".".$option_group->id.".".$option->id;
                                    }else{
                                    $optionId = "newProductOptions.".$product->id.".".$option_group->id;
                                    }
                                    @endphp
                                    @if($option_group->multiple)
                                    <x-checkbox title="{{ $option->name.' ('.setting('currency', '$').''. $option->price.')' }}" name="{{ $optionId }}" :defer="false" />
                                    @else
                                    <x-radio type="radio" title="{{ $option->name.' ('.setting('currency', '$').''. $option->price.')' }}" name="{{ $optionId }}" :defer="false" value="{{ $option->id }}" />
                                    @endif
                                    @endforeach
                                </p>
                            </div>
                            @endforeach
                        </td>
                        <td class="items-center w-1/12 px-2 py-1 border-b">
                            <x-input name="newOrderProductsQtys.{{ $product->id }}" />
                        </td>
                        <td class="items-center w-2/12 px-2 py-1 border-b">
                            {{-- actions --}}
                            <x-buttons.plain wireClick="$emit('removeModel', '{{ $product->id }}' )" bgColor="bg-red-500">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </x-buttons.plain>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- lo --}}
            <hr class="my-4" />

            {{-- Customer --}}
            <livewire:component.autocomplete-input title="{{ __('Customer') }}" column="name" model="User" emitFunction="autocompleteUserSelected" onclearCalled="clearAutocompleteFieldsEvent" />
            <x-checkbox title="{{ __('Pickup Order') }}" name="isPickup" :defer="false" />
            @if(!$isPickup ?? false)

            <livewire:component.autocomplete-input title="{{ __('Delivery Address') }}" column="name" model="DeliveryAddress" emitFunction="autocompleteDeliveryAddressSelected" updateQueryClauseName="addressQueryClasueUpdate" :queryClause="$addressSearchClause" onclearCalled="clearAutocompleteFieldsEvent" />
            @endif
            <x-select title="{{ __('Payment Method') }}" :options="$paymentMethods ?? []" name="paymentMethodId" />


            <hr class="my-4" />

            <div class="flex items-center">
                <div class="flex-grow">
                    <x-input title="{{ __('Coupon Code') }}" name="couponCode" />
                </div>
                <div class="w-2/12 mt-6 ml-2">
                    <p></p>
                    <x-buttons.primary wireClick="applyDiscount">
                        {{ __('APPLY') }}
                        </x-buttons.plain>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-input title="{{ __('Tip').'('.setting('currency', '$').')' }}" name="tip" />
                <x-input title="{{ __('Note') }}" name="note" />
            </div>
            <hr class="my-4" />




        </x-modal-lg>
    </div>

    {{-- ORDER PLACEMENT  --}}
    <div x-data="{ open: @entangle('showSummary') }">
        <x-modal-lg confirmText="{{ __('Place Order') }}" action="saveNewOrder" onCancel="$set('showSummary', false)">
            <p class="text-xl font-semibold">{{ __('Order Summary') }}</p>
            {{-- order summary  --}}
            <div class="grid grid-cols-1 md:grid-cols-2">
                <x-details.item title="{{ __('Customer') }}" text="{{ $newOrder->user->name ?? '' }}" />
                @if(!$isPickup)
                <x-details.item title="{{ __('Delivery Address') }}" text="{{ $newOrder->delivery_address->address ?? 'Pickup' }}" />
                @endif
                <x-details.item title="{{ __('Note') }}" text="{{ $newOrder->note ?? '' }}" />
                <x-details.item title="{{ __('Payment Method') }}" text="{{ $newOrder->payment_method->name ?? '' }}" />
            </div>
            <hr class="my-4" />
            {{-- vendor details  --}}
            <x-details.item title="{{ __('Vendor') }}" text="{{ $newOrder->vendor->name ?? '' }}" />
            {{-- products  --}}
            <p class="mt-4 mb-2 font-medium text-md">{{ __('Products') }}</p>
            <table class="w-full p-2 border rounded-sm">
                <thead>
                    <tr>
                        <th class="w-1/12 p-2 bg-gray-300 border-b">S/N</th>
                        <th class="w-3/12 p-2 bg-gray-300 border-b">Name</th>
                        <th class="w-4/12 p-2 bg-gray-300 border-b">Options</th>
                        <th class="w-2/12 p-2 bg-gray-300 border-b">QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newOrderProducts ?? [] as $key => $product)
                    <tr>
                        <td class="items-center w-1/12 px-2 py-1 border-b">
                            <span>{{ $key + 1 }}</span>
                        </td>
                        <td class="items-center w-2/12 px-2 py-1 border-b">
                            {{ $product->name }}
                        </td>
                        <td class="items-center w-4/12 px-2 py-1 border-b">

                            @if(!empty($newProductSelectedOptions))
                            @foreach($newProductSelectedOptions[$product->id] ?? [] as $key => $productSelectedOption)
                            <p>
                                {{ $productSelectedOption['name'] }}
                                {{ setting('currency', '$').''.$productSelectedOption['price'] }}
                            </p>
                            @endforeach
                            @endif
                        </td>
                        <td class="items-center w-1/12 px-2 py-1 border-b">
                            {{ $newOrderProductsQtys[$product->id] ?? '1' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr class="my-4" />
            <div class="">

                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Subtotal') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="{{ setting('currency', '$') }}{{ $newOrder->sub_total ?? '' }}" />
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Discount') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="- {{ setting('currency', '$') }}{{ $newOrder->discount ?? '' }}" />
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Tax') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="+ {{ setting('currency', '$') }}{{ $newOrder->tax ?? '' }}" />
                    </div>
                </div>
                @if (!$isPickup)

                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Delivery Fee') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="+ {{ setting('currency', '$') }}{{ $newOrder->delivery_fee ?? '0' }}" />
                    </div>
                </div>
                @endif
                {{-- driver tip --}}
                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Driver Tip') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="+ {{ setting('currency', '$') }}{{ $newOrder->tip ?? '0' }}" />
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-20 border-b">
                    <x-label title="{{ __('Total') }}" />
                    <div class="w-6/12 md:w-4/12 lg:w-2/12">
                        <x-details.p text="{{ setting('currency', '$') }}{{ $newOrder->total ?? '' }}" />
                    </div>
                </div>



        </x-modal-lg>
    </div>

</div>
