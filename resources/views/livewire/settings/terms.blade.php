<div>

    <x-baseview title="Terms & Condition">

        <x-form action="saveTermsSettings" backPressed="$set('showTerms', false)">
            <div class="w-full md:w-4/5 lg:w-5/12">

                <div class="mb-4">
                    <x-label title="{{ __('Terms & Condition') }}"/>
                    <p class="text-xs italic text-red-500">* Html code allowed</p>
                </div>
                <textarea id="terms" wire:model.defer="terms" class="w-full h-64 p-2 border border-black rounded-sm"></textarea>
                <x-buttons.primary title="Save Changes" />

            <div>
        </x-form>

    </x-baseview>



</div>




