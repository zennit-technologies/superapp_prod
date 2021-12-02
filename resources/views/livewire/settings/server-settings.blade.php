@section('title', 'Server Settings')
<div>

    <x-baseview title="Server Settings">



        <div class='grid grid-cols-1 gap-10 md:grid-cols-2'>
            <div>
                <x-form action="saveMailSettings">
                    <p class="text-2xl font-body">Mail Setting</p>
                    <div class="p-2 my-2 bg-gray-100 border border-gray-300 rounded">
                        <p>Popular smtp servers</p>
                        <span class="mx-1"><a href="https://www.mailgun.com/" target="_blank" class="text-xs underline text-primary-300">mailgun.com</a></span>
                        <span class="mx-1"><a href="https://www.sendgrid.com/" target="_blank" class="text-xs underline text-primary-300">sendgrid.com</a></span>
                        <span class="mx-1"><a href="https://www.sendinblue.com/" target="_blank" class="text-xs underline text-primary-300">sendinblue.com</a></span>
                        <span class="mx-1"><a href="https://www.mailjet.com/" target="_blank" class="text-xs underline text-primary-300">mailjet.com</a></span>
                        <span class="mx-1"><a href="https://www.sparkpost.com/" target="_blank" class="text-xs underline text-primary-300">sparkpost.com</a></span>
                    </div>
                    <x-input title="Host" name="mailHost" />
                    <x-input title="Port" name="mailPort" />
                    <x-input title="Username" name="mailUsername" />
                    <x-input title="Password" name="mailPassword" type="password" />
                    <x-input title="From Email" name="mailFromAddress" />
                    <x-input title="From Name" name="mailFromName" />
                    <x-input title="Encryption" name="mailEncryption" placeholder="tls" />
                    <div class="h-1 my-4 bg-gray-500 border-3"></div>
                    <x-input title="Android App Download Link" name="androidDownloadLink" />
                    <x-input title="iOS App Download Link" name="iosDownloadLink" />
                    <x-buttons.primary title="Save Changes" />
                </x-form>
            </div>
            {{-- Test email seetings is working --}}
            <div>
                <x-form action="testMailSettings">
                    <p class="text-2xl font-body">Test Mail Setting</p>
                    <x-input title="Email" name="testEmail" type="email" />
                    <x-input title="Subject" name="testSubject" />
                    <x-textarea title="Body" name="testBody" />
                    <x-buttons.primary title="Send" />
                </x-form>
            </div>
        </div>

    </x-baseview>

</div>
