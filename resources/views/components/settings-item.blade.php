<div class="flex items-center p-4 text-lg bg-white border rounded cursor-pointer hover:shadow-md" wire:click='{{ $wireClick ?? '' }}' >
    <div class="{{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}"> {{ $slot ?? '' }} </div>
    {{ $title }}
</div>
