@section('title', __('Settings'))
<div>

    <x-baseview title="{{__('Settings')}}">
        
        <div class="grid grid-cols-1 gap-6 mt-10 md:grid-cols-2 lg:grid-cols-3">

            {{-- OneSignal settings --}}
            <x-settings-item title="{{__('Push notification (Firebase)')}}" wireClick="notificationSetting">
                <x-heroicon-o-speakerphone class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

            {{-- app settings --}}
            <x-settings-item title="{{__('Web App Settings')}}" wireClick="appSettings">
                <x-heroicon-o-cog class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

            {{-- custom message --}}
            <x-settings-item title="{{__('Custom Notification Messages')}}" wireClick="customNotificationSettings">
                <x-heroicon-o-bell class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>


            {{-- Privacy policy --}}
            <x-settings-item title="{{__('Privacy & Policy')}}" wireClick="privacySettings">
                <x-heroicon-o-eye-off class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

            {{-- Contact info --}}
            <x-settings-item title="{{__('Contact Info')}}" wireClick="contactSettings">
                <x-heroicon-o-chat class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

            {{-- Terms and conditions --}}
            <x-settings-item title="{{__('Terms & Conditions')}}" wireClick="termsSettings">
                <x-heroicon-o-clipboard-list class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

            {{-- Terms and conditions --}}
            <x-settings-item title="{{__('Page Settings')}}" wireClick="pageSettings">
                <x-heroicon-o-book-open class="w-5 h-5 {{ setting('localeCode') == 'ar' ? 'ml-4':'mr-4' }}" />
            </x-settings-item>

        </div>
    </x-baseview>

</div>
