@section('title',  __('Auto-assignments') )
<div>

    <x-baseview title="{{ __('Auto-assignments') }} ">
        <div wire:poll.20000ms="refreshDataTable">
            <livewire:tables.auto-assignment-table />
        </div>
    </x-baseview>


</div>
