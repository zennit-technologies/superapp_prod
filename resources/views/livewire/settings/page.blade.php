<div>

    <x-baseview title="Page Settings">

        <x-form action="savePageSettings" backPressed="$set('showPageSetting', false)">
            <div class="w-full md:w-4/5 lg:w-5/12">

                <x-input title="{{ __('Driver Verification Document Instructions') }}" name="driverDocumentInstructions" />
                <x-input title="{{ __('Vendor Verification Document Instructions') }}" name="vendorDocumentInstructions" />
                <x-buttons.primary title="Save Changes" />

            <div>
        </x-form>

    </x-baseview>



</div>




