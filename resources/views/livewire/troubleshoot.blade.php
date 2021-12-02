@section('title',  __('Troubleshoot') )
<div>

    <x-baseview title="{{ __('Troubleshoot') }} ">
        <div class="grid grid-cols-1 gap-6 mt-10 md:grid-cols-2 lg:grid-cols-3">

            {{-- fix image --}}
            <x-settings-item title="{{__('Fix Image(Not Loading)')}}" wireClick="fixImage">
                <x-heroicon-o-photograph class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>
            {{-- fix image --}}
            <x-settings-item title="{{__('Clear Cache')}}" wireClick="fixCache">
                <x-heroicon-o-desktop-computer class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

         

        </div>
    </x-baseview>


</div>
