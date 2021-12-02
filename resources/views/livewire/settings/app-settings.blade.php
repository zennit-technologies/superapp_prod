@section('title', __('Mobile App Settings'))
<div>

    <x-baseview title="{{ __('Mobile App Settings') }}">

        <x-form action="saveAppSettings">

            <div class="">
                <div class='grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3'>
                    <x-input title="{{ __('App Name') }}" name="appName" />
                    <div>
                        <x-input title="Google Map Key" name="googleMapKey" />
                        <p class="mt-1 text-xs">Insert google maps key <br />
                            ( <a href="https://console.developers.google.com/apis/dashboard" target="_blank" class="underline text-primary-500">https://console.developers.google.com/apis/dashboard</a>
                            )
                        </p>
                    </div>
                    {{-- What3words --}}
                    <div>
                        <x-input title="What3words Api Key" name="what3wordsApiKey" />
                        <a href="https://what3words.com/select-plan?currency=USD" target="_blank" class="mt-1 text-xs text-gray-500 underline">Get api key</a>
                    </div>

                    {{-- country code --}}
                    <div>
                        <x-input title="Country Code" name="appCountryCode" />
                        <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank" class="mt-1 text-xs text-gray-500 underline">{{ __('List Of Country Codes') }}</a>
                        <p class="text-sm text-gray-500">
                            {{ __('Note: For example if you want to allow phone from Ghana you enter GH') }}
                        </p>
                    </div>


                    {{-- refer --}}
                    <div class="block mt-4 text-sm">
                        <p>{{ __('Refer System') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="enableReferSystem" :defer="true" />
                    </div>
                    <div x-data="{ open: @entangle('enableReferSystem') }">
                        <x-input title="{{ __('Refer Amount') }}" name="referRewardAmount" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <p>{{ __('Multiple Stops(Parcel Delivery)') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="enableParcelMultipleStops" :defer="true" />
                    </div>
                    <x-input title="{{ __('Max Stops(Parcel Delivery)') }}" name="maxParcelStops" type="number" />
                    {{-- clear firebase --}}
                    <div class="block mt-4 text-sm">
                        <p>{{ __('Clear Firebase after order') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="clearFirestore" :defer="true" />
                        <p class="text-xs text-gray-500">
                            {{ __('Note: This is to reduce the size of your firebase firestore, by removing completed or failed orders from the firebase firestore') }}
                        </p>
                    </div>
                </div>

                {{-- Auth Layout --}}
                <p class="pt-4 mt-10 text-2xl border-t">Auth Related</p>
                <div class='grid grid-cols-1 gap-4 mb-10 md:grid-cols-2 lg:grid-cols-3'>
                    {{-- enableOTPLogin --}}
                    <div class="block mt-4 text-sm">
                        <p>{{ __('OTP Login') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="enableOTPLogin" :defer="true" />
                    </div>
                    {{-- Working --}}
                    <x-select :options="$smsGateways" :title="__('Phone OTP for verification')" name="otpGateway" />
                    <div></div>
                </div>
                <div class='grid grid-cols-1 gap-4 mb-10 md:grid-cols-4'>
                    <div class="block mt-4 text-sm">
                        <p>Google Login</p>
                        <x-checkbox title="Enable" name="googleLogin" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Apple Login</p>
                        <x-checkbox title="Enable" name="appleLogin" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Facebook Login</p>
                        <x-checkbox title="Enable" name="facebbokLogin" :defer="true" />
                    </div>
                </div>


                {{-- App Layout --}}
                <p class="pt-4 mt-10 text-2xl border-t">App Layout</p>
                <div class='grid grid-cols-1 gap-4 mb-10 md:grid-cols-2 lg:grid-cols-3'>
                    <div class="block mt-4 text-sm">
                        <p>Calculate Distance via Google Map</p>
                        <x-checkbox title="Enable" name="enableGoogleDistance" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Single-Vendor Mode</p>
                        <x-checkbox title="Enable" name="enableSingleVendor" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Chat Option</p>
                        <x-checkbox title="Enable" name="enableChat" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Parcel Vendor By Location</p>
                        <x-checkbox title="Enable" name="enableParcelVendorByLocation" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Order Tracking</p>
                        <x-checkbox title="Enable" name="enableOrderTracking" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Allow Prescription</p>
                        <x-checkbox title="Enable" name="enableUploadPrescription" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Proof of delivery by delivery boy</p>
                        <x-checkbox title="Enable" name="enableProofOfDelivery" :defer="true" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <p>Proof type</p>
                        <x-select :options="['none','code','signature']" title="" name="orderVerificationType" />
                    </div>
                </div>


                {{-- Driver releated settings --}}
                <p class="pt-4 mt-10 text-2xl border-t">{{ __('Driver App Settings') }}</p>
                <div class='grid grid-cols-1 gap-4 mb-10 md:grid-cols-2 lg:grid-cols-3'>


                    <div class="block mt-4 text-sm">
                        <p>{{ __('Driver Wallet System') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="enableDriverWallet" :defer="true" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <p>{{ __('Vendor Earning Enable(irrespective of the payment method used)') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="vendorEarningEnabled" :defer="true" />
                    </div>


                    <div class="block mt-4 text-sm">
                        <p>{{ __('Driver Wallet Balance Require') }}</p>
                        <x-checkbox title="{{ __('Enable') }}" name="driverWalletRequired" :defer="true" description="{{ __('This means driver must have enough in wallet balance irrespective of the payment method') }}" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Accept Time Duration(seconds)') }}" name="alertDuration" type="number" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Driver order search radius') }}(KM)" name="driverSearchRadius" type="number" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Driver Max Acceptable Order') }}" name="maxDriverOrderAtOnce" type="number" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Number of driver to be notified of new order') }}" name="maxDriverOrderNotificationAtOnce" type="number" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Resend rejected auto-assignment notification(minutes)') }}" name="clearRejectedAutoAssignment" type="number" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Emergency Contact for drivers and customers') }}" name="emergencyContact" />
                    </div>

                    {{-- Location updating --}}
                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Location Update Distance(Meter)') }}" name="distanceCoverLocationUpdate" />
                    </div>
                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Location Update Time(Seconds)') }}" name="timePassLocationUpdate" />
                    </div>
                </div>
                {{-- Finance --}}
                <p class="pt-4 mt-10 text-2xl border-t">{{ __("Finance") }}</p>
                <div class='grid grid-cols-1 gap-4 mb-10 md:grid-cols-2 lg:grid-cols-3'>
                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('General Drivers Commission') }}(%)" name="driversCommission" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('General Vendors Commission') }}(%)" name="vendorsCommission" />
                    </div>

                    <div class="block mt-4 text-sm">
                        <x-input title="{{ __('Minimum Wallet Topup Ammount') }}" name="minimumTopupAmount" />
                    </div>

                </div>




                <p class="pt-4 mt-4 text-2xl border-t">Theme</p>
                <p class="mt-4 text-lg border-b">Main Colors</p>
                <div class='grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3'>
                    <x-input title="Accent Color" name="accentColor" type="color" class="h-10" />
                    <x-input title="Primary Color" name="primaryColor" type="color" class="h-10" />
                    <x-input title="Primary Dark Color" name="primaryColorDark" type="color" class="h-10" />
                </div>
                {{-- other --}}
                <p class="mt-4 text-lg border-b">Onboarding Colors</p>
                <div class='grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3'>
                    <x-input title="Onboarding Page 1 Background Color" name="onboarding1Color" type="color" class="h-10" />
                    <x-input title="Onboarding Page 2 Background Color" name="onboarding2Color" type="color" class="h-10" />
                    <x-input title="Onboarding Page 3 Background Color" name="onboarding3Color" type="color" class="h-10" />
                    {{-- next --}}
                    <x-input title="Onboarding Indicator Dot Color" name="onboardingIndicatorDotColor" type="color" class="h-10" />
                    <x-input title="Onboarding Indicator Active Dot Color" name="onboardingIndicatorActiveDotColor" type="color" class="h-10" />
                </div>
                <p class="mt-4 text-lg border-b">Order Status Colors</p>
                <div class='grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3'>
                    <x-input title="Open Color" name="openColor" type="color" class="h-10" />
                    <x-input title="Close Color" name="closeColor" type="color" class="h-10" />
                    <x-input title="Delivery Color" name="deliveryColor" type="color" class="h-10" />
                    <x-input title="Pickup Color" name="pickupColor" type="color" class="h-10" />
                    <x-input title="Rating Color" name="ratingColor" type="color" class="h-10" />
                </div>
                <p class="mt-4 text-lg border-b">Order Status Colors</p>
                <div class='grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3'>
                    {{-- other status colors --}}
                    <x-input title="Pending Color" name="pendingColor" type="color" class="h-10" />
                    <x-input title="Preparing Color" name="preparingColor" type="color" class="h-10" />
                    <x-input title="Enroute Color" name="enrouteColor" type="color" class="h-10" />
                    <x-input title="Failed Color" name="failedColor" type="color" class="h-10" />
                    <x-input title="Cancelled Color" name="cancelledColor" type="color" class="h-10" />
                    <x-input title="Delivered Color" name="deliveredColor" type="color" class="h-10" />
                    <x-input title="Successful Color" name="successfulColor" type="color" class="h-10" />
                </div>
                <x-buttons.primary title="Save Changes" />
                <div>
        </x-form>

    </x-baseview>

</div>
