@section('title', __('Payment Methods') )
<div>

    <x-baseview title="{{ __('Payment Methods') }}">
        <livewire:tables.vendor-payment-method-table />
    </x-baseview>

    {{-- assign form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Assign') }}" action="assignPaymentMethods" :clickAway="false">

            <p class="text-xl font-semibold">{{ __('Assign Payment Methods To Vendor') }}</p>
            <x-select2 title="{{ __('Managers') }}" :options="$paymentMethods" name="paymentMethodIds" id="paymentMethodSelect2" :multiple="true"
                width="100" :ignore="true" />

        </x-modal>
    </div>
</div>


