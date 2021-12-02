@section('title', __('Subscriptions') )
<div>

    <x-baseview title="{{ __('Subscriptions') }}">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach($subscriptions as $subscription)
            <div class="overflow-hidden text-center border rounded-md shadow cursor-pointer hover:shadow-lg" wire:click="subscriptionSelected('{{ $subscription->id }}')">
                <p class="p-5 text-5xl font-bold text-white bg-primary-400">
                    <span class="text-3xl font-medium">{{ setting('currency', '$') }}</span>
                    {{ $subscription->amount }}
                </p>
                <p class="px-2 mt-4 mb-1 text-xl">{{ $subscription->name }}</p>
                <p class="px-2 mb-4 text-2xl font-medium">{{ $subscription->days }} <span class="text-xl font-medium">{{ _('Days') }}</span></p>
                @if (!empty($subscription->qty) )
                <p class="px-2 text-2xl font-medium">{{ $subscription->qty }} <span class="text-xl font-medium">{{ _('Qty') }}</span></p>
                <p class="mx-4 mb-4 text-xs text-gray-400">{{ __("Note: Number of products/services/package types allowed per vendor") }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </x-baseview>

    {{-- payment selector model --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal>

            <p class="text-xl font-semibold">{{ __('Subscription Payment') }}</p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($paymentMethods as $paymentMethod)
                <button wire:click='initPayment({{ $paymentMethod->id }})'>
                    <div class="flex items-center p-1 border rounded shadow">
                        <img src="{{ $paymentMethod->photo }}" class="w-2/12 md:w-3/12" />
                        {{ $paymentMethod->name }}
                    </div>
                </button>
                @endforeach
            </div>

        </x-modal>
    </div>

    {{-- payment processing model --}}
    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal :withForm="false">
            @if (($selectedPaymentMethod->slug ?? '') == 'offline')
            @include('livewire.payment.offline.subscription')
            @elseif( ($selectedPaymentMethod->slug ?? '') == "paypal" )
            <div id="paypal-button-container" class="py-12"></div>
            @endif
            {{-- paytm --}}
            @include('livewire.payment.gateways.paytm')
            {{-- payU --}}
            @include('livewire.payment.gateways.payu')


            @if (($selectedPaymentMethod->slug ?? '') != 'offline')
            <p class="w-full p-4 text-sm text-center text-gray-500">{{__('Do not close this window')}}</p>
            @endif

        </x-modal>
    </div>


</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalMethod->public_key ?? '' }}&currency={{ setting('currencyCode', 'USD') }}&intent=capture">
</script>
<script src="{{ asset('js/subscription.js') }}"></script>
{{-- //custom payment  --}}
@endpush
