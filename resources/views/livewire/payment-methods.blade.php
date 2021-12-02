@section('title',  __('Payment Methods') )
<div>

    <x-baseview title="{{ __('Payment Methods') }}">
        <livewire:tables.payment-method-table />
    </x-baseview>

    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="{{ __('Update') }}" action="update">

            <p class="text-xl font-semibold">{{ __('Edit Payment Method') }}</p>
            <x-input title="{{ __('Name') }}" name="name" />
            <x-input title="Secret Key" name="secret_key" />
            <x-input title="Public Key" name="public_key" />
            <x-input title="Hash Key" name="hash_key" />
            <x-label title="{{ __('Instruction(offline payment)') }}" />
            <textarea wire:model.defer="instruction" class="w-full h-40 p-2 mt-1 border rounded"></textarea>
            <x-media-upload
                        title="{{ __('Photo') }}"
                        name="photo"
                        preview="{{ $selectedModel->photo ?? '' }}"
                        :photo="$photo"
                        :photoInfo="$photoInfo"
                        types="PNG or JPEG"
                        rules="image/*" />
            <x-checkbox
                    title="{{ __('Active') }}"
                    name="isActive" :defer="false" />


        </x-modal>
    </div>
</div>


