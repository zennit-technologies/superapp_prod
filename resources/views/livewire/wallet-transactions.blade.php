@section('title', __('Wallet Transactions') )
<div>

    <x-baseview title="{{ __('Wallet Transactions') }}">
        <livewire:tables.wallet-transaction-table />
    </x-baseview>

    {{-- details modal --}}
    <div x-data="{ open: @entangle('showDetails') }">
        <x-modal>

            <p class="text-xl font-semibold">
                {{ __('Wallet Transactions Details') }}
            </p>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-details.item title="{{ __('Ref') }}" text="{{ $selectedModel->ref ?? '' }}" />
                <x-details.item title="{{ __('Transaction Code') }}" text="">
                    <p class="break-words">{{ $selectedModel->session_id ?? '' }}</p>
                </x-details.item>
                <x-details.item title="{{ __('User') }}" text="{{ $selectedModel->wallet->user->name ?? '' }}" />
                <x-details.item title="{{ __('Amount') }}" text="{{ setting('currency', '$') }} {{ number_format( $selectedModel->amount ?? 0.00, 2 ) }}" />
                <x-details.item title="{{ __('Payment Method') }}" text="{{ $selectedModel->payment_method->name ?? '' }}" />
                <x-details.item title="{{ __('Status') }}" text="{{ $selectedModel->status ?? '' }}" />
            </div>
            <hr class="mt-4" />
            <x-details.item title="{{ __('Reason') }}" text="{{ $selectedModel->reason ?? '' }}" />
            @if($selectedModel != null && $selectedModel->payment_method != null && $selectedModel->payment_method->slug == "offline")
            <img src="{{ $selectedModel->photo ?? '' }}" class="mt-6 border rounded-sm" />
            @endif
        </x-modal>
    </div>

</div>
