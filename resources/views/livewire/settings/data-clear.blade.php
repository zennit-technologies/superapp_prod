@section('title', __('Clear Data'))
<div>

    <x-baseview title="{{__('Clear Data')}}">

        <div class="grid grid-cols-1 gap-6 mt-10 md:grid-cols-2 lg:grid-cols-3">


            <x-settings-item title="{{__('Orders')}}" wireClick="confirmAction('Order','clearOrders')" >
                <x-heroicon-o-shopping-bag class="w-5 h-5" />
            </x-settings-item>


            <x-settings-item title="{{__('Products')}}" wireClick="confirmAction('Product','clearProducts')" >
                <x-heroicon-o-archive class="w-5 h-5" />
            </x-settings-item>


            <x-settings-item title="{{__('Vendors')}}" wireClick="confirmAction('Vendor','clearVendors')" >
                <x-heroicon-o-shopping-cart class="w-5 h-5" />
            </x-settings-item>


            <x-settings-item title="{{__('Users')}}" wireClick="confirmAction('User','clearUsers')" >
                <x-heroicon-o-user-group class="w-5 h-5" />
            </x-settings-item>



        </div>

        <div x-data="{ open: @entangle('showCreate') }">
            <x-modal confirmText="{{ __('Clear') }}" action="{{ $actionCalled ?? '' }}">
                <p class="text-xl font-semibold">{{ __('Clear Data') }}</p>
                <p class="">{{ __('Are you sure you want to clear') }} {{ $model ?? '' }}?</p>
            </x-modal>
        </div>

    </x-baseview>

</div>
