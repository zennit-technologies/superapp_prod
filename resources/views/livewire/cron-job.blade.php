@section('title',  __('CRON Job') )
<div>

    <x-baseview title="{{ __('CRON Job') }} ">
        <div class="p-5 border shadow">
            <p>
                How to setup cron job on your server: <a href="https://bit.ly/3DoNGBm"
                target="_blank" class="font-bold underline text-primary-500">https://bit.ly/3DoNGBm</a>
            </p>
            <hr class="my-6" />
            <p class="mb-2 text-2xl font-bold">External cron job managers</p>
            <p class="mb-2">For external cron job managers like(e.g https://cron-job.org). Copy the url below:</p>
            <div class="items-center block w-full space-y-2 md:space-x-2 md:flex">
                <div class="w-full p-2 bg-gray-200 rounded md:w-9/12 broder">
                    {{ route('cron.job') }}?key={{ $cronJobKey ?? '' }}
                </div>
                <div class="w-full md:w-3/12">
                    <x-buttons.primary title="Generate New Key" wireClick="genNewKey" noMargin="true"/>
                </div>
            </div>

            
        </div>
    </x-baseview>


</div>
