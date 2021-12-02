@section('title', 'Become A Partner')
<div>
    <div class="items-center min-h-screen bg-gray-50 md:flex ">
        <div class="flex flex-col overflow-y-auto md:flex-row">
            {{-- image --}}
            <div class="hidden h-screen md:block md:w-1/2">
                <div class="w-full h-full">
                <img aria-hidden="true" class="object-cover w-full h-full"
                    src="{{ setting('registerImage', asset('images/login-office.jpeg')) }}"
                    alt="Office" />
            </div>
            </div>
            {{-- form --}}
            <div
                class="w-11/12 h-full max-w-xl mx-auto my-12 overflow-hidden bg-white rounded-lg shadow-xl md:my-auto md:max-w-2xl ">
                <div class="flex flex-col overflow-y-auto md:flex-row">
                    <div class="flex items-center justify-center w-full p-6 sm:p-12 ">
                        <div class="w-full">
                            <h1 class="mb-4 text-3xl font-bold text-gray-700 uppercase">
                                {{ __('Become a partner') }} </h1>
                            
                                    {{--  tabs  --}}
                                <div x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'driver' }"
                                    id="tab_wrapper">
                                    
                                    <nav class="mb-8">
                                        <a :class="tab==='driver' ? 'bg-primary-500 text-white' : 'bg-primary-200 text-gray-500'"
                                         class="p-2 font-semibold text-white rounded-t-lg text-md"
                                            @click.prevent="tab = 'driver'; window.location.hash = 'driver'"
                                            href="#">{{ __('Driver') }}</a>
                                            <a :class="tab==='vendor' ? 'bg-primary-500 text-white' : 'bg-primary-200 text-gray-500'"
                                            class="p-2 font-semibold text-white rounded-t-lg text-md"
                                            @click.prevent="tab = 'vendor'; window.location.hash = 'vendor'"
                                            href="#">{{ __('Vendor') }}</a>
                                    </nav>

                                    
                                    {{--  tabs section  --}}
                                    {{--  driver account registration  --}}
                                    <div x-show="tab === 'driver'">
                                        <form wire:submit.prevent="driverSignUp">
                                            @csrf
                                            <x-input title="{{ __('Name') }}" name="name" placeholder="John" />
                                            <x-input title="{{ __('Email') }}" name="email" placeholder="info@mail.com" />
                                            <x-input title="{{ __('Phone') }}" name="phone" placeholder="+2335575..." />
                                            <x-input title="{{ __('Login Password') }}" name="password" type="password"
                                            placeholder="**********************" />
                                            <x-input title="{{ __('Referral Code') }}" name="referalCode" placeholder="" />
                                            <hr class="my-4"/>
                                            <p class="font-light">{{ __('Documents') }}</p>
                                            <livewire:component.multiple-media-upload 
                                            title="{{ setting('page.settings.driverDocumentInstructions', __('Documents')) }}" 
                                                types="PNG or JPEG" 
                                                fileTypes="image/*" 
                                                emitFunction="driverDocumentsUploaded" />
                                                <x-input-error message="{{ $errors->first('driverDocuments') }}" />
                                            <hr class="my-4"/>
                                            <div class="flex items-center my-3">
                                                <x-checkbox name="agreedDriver" :defer="false" :noMargin="true"> <span>I agree with <a href="{{ route('terms') }}" target="_blank" class="font-bold text-primary-500 hover:underline">terms and conditions</a></span>
                                                </x-checkbox>
                                            </div>
                                            <x-buttons.primary title="{{__('Sign Up')}}" />
                                        </form>
                                    </div>
                                    {{--  vendor account registration  --}}
                                    <div x-show="tab === 'vendor'">
                                        <form wire:submit.prevent="vendorSignUp">
                                            @csrf
                                            <h1 class="mb-4 font-semibold text-gray-700 text-md">
                                                {{ __('Business Information') }} </h1>
                                            <x-input title="{{ __('Business Name') }}" name="vendor_name" placeholder="" />
                                            {{-- vendor type --}}
                                            <x-select title="{{ __('Vendor Type') }}" :options='$vendorTypes ?? []' name="vendor_type_id" :defer="false" />
                                            <div class="grid grid-cols-2 space-x-4">
                                            <x-input title="{{ __('Email') }}" name="vendor_email" placeholder="info@mail.com" />
                                            <x-input title="{{ __('Phone') }}" name="vendor_phone" placeholder="+2335575..." />
                                            </div>
                                            {{--  documents  --}}
                                            <hr class="my-4"/>
                                            <p class="font-light">{{ __('Documents') }}</p>
                                            <livewire:component.multiple-media-upload 
                                                title="{{ setting('page.settings.vendorDocumentInstructions', __('Documents')) }}" 
                                                types="PNG or JPEG" 
                                                fileTypes="image/*" 
                                                emitFunction="vendorDocumentsUploaded" />
                                                <x-input-error message="{{ $errors->first('vendorDocuments') }}" />
                                            
                                            {{--  divider  --}}
                                            <hr class="my-4" />
                                            <h1 class="mb-4 font-semibold text-gray-700 text-md">
                                                {{ __('Personal Information') }} </h1>
                                            <x-input title="{{ __('Name') }}" name="name" placeholder="John" />
                                            <div class="grid grid-cols-2 space-x-4">
                                            <x-input title="{{ __('Email') }}" name="email" placeholder="info@mail.com" />
                                            <x-input title="{{ __('Phone') }}" name="phone" placeholder="+2335575..." />
                                            </div>
                                            <x-input title="{{ __('Login Password') }}" name="password" type="password"
                                            placeholder="**********************" />
                                            <div class="flex items-center my-3">
                                                <x-checkbox name="agreedVendor" :defer="false" :noMargin="true"> <span>I agree with <a href="{{ route('terms') }}" target="_blank" class="font-bold text-primary-500 hover:underline">terms and conditions</a></span>
                                            </x-checkbox>
                                            </div>
                                                <x-buttons.primary title="{{__('Sign Up')}}" />
                                        </form>
                                    </div>
<p class="my-4 text-center">
    {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="ml-2 font-bold text-primary-500 text-md">{{ __('Login') }}</a>
</p>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
