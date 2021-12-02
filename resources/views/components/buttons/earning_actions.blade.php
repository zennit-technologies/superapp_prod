<div class="flex items-center space-x-2">

    <x-buttons.payout :model="$model" />
    @if( ($type ?? '' ) == "drivers")
    @if($model->amount > 0)
        <x-buttons.plain wireClick="$emit('initiateEarningWalletTransfer', {{ $model->id }})">
            {{ __("Transfer") }}
        </x-buttons.plain>
    @endif
    @endif

</div>
