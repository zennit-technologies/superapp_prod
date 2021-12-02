@section('title', __('Settings'))
<div>

    <x-baseview title="{{__('Currencies')}}">
        <livewire:tables.currency-table />
    </x-baseview>

    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{__('Save')}}" action="save">
            <p class="text-xl font-semibold">{{__('New Currency')}}</p>
            <x-input title="{{__('Name')}}" name="name" />
            <x-input title="{{__('Code')}}" name="code" />
            <x-input title="{{__('Country Code')}}" name="country_code" />
            <x-input title="{{__('Symbol')}}" name="symbol" />
        </x-modal>
    </div>

    <div x-data="{ open: @entangle('showEdit') }">
        <x-modal confirmText="Update" action="update">
            <p class="text-xl font-semibold">{{__('Edit Currency')}}</p>
            <x-input title="{{__('Name')}}" name="name" />
            <x-input title="{{__('Code')}}" name="code" />
            <x-input title="{{__('Country Code')}}" name="country_code" />
            <x-input title="{{__('Symbol')}}" name="symbol" />
        </x-modal>
    </div>

</div>
