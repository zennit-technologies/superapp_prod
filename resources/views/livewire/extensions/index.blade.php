@section('title', __('Extensions'))
<div>

    <x-baseview title="{{ __('Extensions') }}" showNew="{{ $showDetails }}"
        actionTitle="{{ __('Install') }}">
        <div class="mt-10"></div>
        @if($showDetails ?? true)

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                {{-- extensions settings --}}
                @foreach($extensions ?? [] as $extension)
                    <x-settings-item title="{{ __($extension->name) }}"
                        wireClick="$emit('{{ $extension->action }}')">
                        {{ svg($extension->icon ?? 'heroicon-o-puzzle')->class('w-5 h-5 mr-4') }}
                    </x-settings-item>
                @endforeach
            </div>

        @endif

        @foreach($extensions ?? [] as $extension)
            @livewire($extension->component,[], key($extension->id))
        @endforeach


    </x-baseview>


    {{-- new form --}}
    <div x-data="{ open: @entangle('showCreate') }">
        <x-modal confirmText="{{ __('Install') }}" action="installExtension">
            <p class="text-xl font-semibold">{{ __('Install Extension') }}</p>
            <x-media-upload
                        title="{{ __('Zipped File') }}"
                        name="photo"
                        :photo="$photo"
                        :photoInfo="$photoInfo"
                        types="Zip"
                        :image="false"
                        rules=".zip" />
        </x-modal>
    </div>

</div>
