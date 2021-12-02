<?php

namespace App\Http\Livewire;

use Exception;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\CommonMarkConverter;
use GeoSot\EnvEditor\Facades\EnvEditor;

class SettingsLivewire extends BaseLivewireComponent
{

    public $showNotification = false;
    public $showApp = false;
    public $showPrivacy = false;
    public $showContact = false;
    public $showTerms = false;
    public $showPageSetting = false;
    public $showCustomNotificationMessage = false;

    // App settings
    public $websiteName;
    public $countryCode;
    public $websiteColor;
    public $websiteLogo;
    public $oldWebsiteLogo;
    public $favicon;
    public $oldFavicon;
    public $loginImage;
    public $oldLoginImage;
    public $registerImage;
    public $oldRegisterImage;
    public $timeZone;
    public $maxScheduledDay;
    public $maxScheduledTime;
    public $minScheduledTime;
    public $autoCancelPendingOrderTime;
    public $defaultVendorRating;

    //Privacy Settings
    public $privacyPolicy;
    public $contactInfo;
    public $terms;

    //firebase
    public $apiKey;
    public $projectId;
    public $messagingSenderId;
    public $appId;
    public $vapidKey;
    public $notifyAdmin;
    public $notifyCityAdmin;

    //
    public $locale;
    public $localeCode;
    public $languages = ["English", "French", "Spanish", "Korean", "German", "Portuguese", "Arabic"];
    public $languageCodes = ["en", "fr", "es", "ko", "de", "pt", "ar"];


    //page settings
    public $driverDocumentInstructions;
    public $vendorDocumentInstructions;

    //Custom notification messages
    public $pending;
    public $preparing;
    public $ready;
    public $enroute;
    public $completed;
    public $cancelled;
    public $failed;
    public $managerPreparingMssage;
    public $managerEnrouteMsg;

    protected $listeners = [
        'deleteModel',
        'refreshTable' => '$refresh',
    ];


    public function render()
    {
        if ($this->showNotification) {
            return view('livewire.settings.notification');
        } else if ($this->showApp) {
            return view('livewire.settings.web-app-settings');
        } else if ($this->showPrivacy) {
            return view('livewire.settings.privacy-policy');
        } else if ($this->showContact) {
            return view('livewire.settings.contact');
        } else if ($this->showTerms) {
            return view('livewire.settings.terms');
        } else if ($this->showPageSetting) {
            return view('livewire.settings.page');
        } else if ($this->showCustomNotificationMessage) {
            return view('livewire.settings.custom_notification_message');
        } else {
            return view('livewire.settings.index');
        }
    }

    //Notification settings
    public function notificationSetting()
    {
        $this->apiKey = "XXXXXXXXXXXX";
        $this->projectId = setting('projectId', "");
        $this->messagingSenderId = setting('messagingSenderId', "");
        $this->appId = setting('appId', "");
        $this->vapidKey = "XXXXXXXXXXXX";
        $this->notifyAdmin = (bool) setting('notifyAdmin', "0");
        $this->notifyCityAdmin = (bool) setting('notifyCityAdmin', "0");
        $this->showNotification = true;
    }

    public function saveNotificationSetting()
    {

        try {

            $this->isDemo();

            setting([
                "apiKey" => ($this->apiKey == "XXXXXXXXXXXX") ? setting('apiKey', 'XXXXXXXXXXXX') : $this->apiKey,
                "projectId" => $this->projectId,
                "messagingSenderId" => $this->messagingSenderId,
                "appId" => $this->appId,
                "vapidKey" => ($this->vapidKey == "XXXXXXXXXXXX") ? setting('vapidKey', 'XXXXXXXXXXXX') : $this->vapidKey,
                "notifyAdmin" => $this->notifyAdmin ?? 0,
                "notifyCityAdmin" => $this->notifyCityAdmin ?? 0,
            ])->save();

            //change firenase service worker js file
            $file_name = base_path() . "/public/firebase-messaging-sw.js";
            $this->fileEditContents($file_name, "11", "messagingSenderId: '" . $this->messagingSenderId . "',");

            if ($this->photo != null) {

                $this->validate([
                    "photo" => "required|mimes:json",
                ]);




                //
                $serviceKeyPath = $this->photo->storeAs('vault', 'firebase_service.json');

                setting([
                    'serviceKeyPath' =>  $serviceKeyPath ?? "",
                ])->save();
            }

            $this->showSuccessAlert(__("Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Settings save failed!"));
        }
    }

    //App settings
    public function appSettings()
    {
        $this->websiteName = setting('websiteName', env("APP_NAME"));
        $this->websiteColor = setting('websiteColor', '#21a179');
        $this->countryCode = setting('countryCode', "GH");
        $this->timeZone = setting('timeZone', "UTC");
        $this->maxScheduledDay = setting('maxScheduledDay', 5);
        $this->maxScheduledTime = setting('maxScheduledTime', 2);
        $this->minScheduledTime = setting('minScheduledTime', 2);
        $this->autoCancelPendingOrderTime = setting('autoCancelPendingOrderTime', 30);
        $this->defaultVendorRating = setting('defaultVendorRating', 5);
        $this->oldWebsiteLogo = setting('websiteLogo', asset('images/logo.png'));
        $this->oldFavicon = setting('favicon', asset('images/logo.png'));
        $this->oldLoginImage = setting('loginImage', asset('images/login-office.jpeg'));
        $this->oldRegisterImage = setting('registerImage', asset('images/login-office.jpeg'));
        $this->locale = setting('locale', 'en');
        $this->showApp = true;
    }

    public function saveAppSettings()
    {

        $this->validate([
            "websiteLogo" => "sometimes|nullable|image|max:1024",
            "favicon" => "sometimes|nullable|image|mimes:png|max:48",
            "loginImage" => "sometimes|nullable|image|max:3072",
            "registerImage" => "sometimes|nullable|image|max:3072",
        ]);

        try {

            $this->isDemo();

            // store new logo
            if ($this->websiteLogo) {
                $this->oldWebsiteLogo = Storage::url($this->websiteLogo->store('public/logos'));
            }

            // store new logo
            if ($this->favicon) {
                $this->oldFavicon = Storage::url($this->favicon->store('public/favicons'));
            }

            // store new logo
            if ($this->loginImage) {
                $this->oldLoginImage = Storage::url($this->loginImage->store('public/auth/login'));
            }

            // store new logo
            if ($this->registerImage) {
                $this->oldRegisterImage = Storage::url($this->registerImage->store('public/auth/register'));
            }


            //
            EnvEditor::editKey("APP_NAME", "'" . $this->websiteName . "'");
            $selectedLanguageKey = array_search($this->locale, $this->languages);

            $appSettings = [
                'websiteName' =>  $this->websiteName,
                'websiteColor' =>  $this->websiteColor,
                'locale' =>  $this->locale,
                'localeCode' =>  $this->languageCodes[$selectedLanguageKey],
                'countryCode' =>  $this->countryCode,
                'timeZone' =>  $this->timeZone,
                'maxScheduledDay' =>  $this->maxScheduledDay,
                'maxScheduledTime' =>  $this->maxScheduledTime,
                'minScheduledTime' =>  $this->minScheduledTime,
                'autoCancelPendingOrderTime' =>  $this->autoCancelPendingOrderTime,
                'defaultVendorRating' =>  $this->defaultVendorRating,
                'websiteLogo' =>  $this->oldWebsiteLogo,
                'favicon' =>  $this->oldFavicon,
                'loginImage' =>  $this->oldLoginImage,
                'registerImage' =>  $this->oldRegisterImage,
            ];

            // update the site name
            setting($appSettings)->save();



            $this->showSuccessAlert(__("App Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("App Settings save failed!"));
        }
    }
    //custom notification messages
    public function customNotificationSettings()
    {
        $this->pending = setting("order.notification.message.pending", "");
        $this->preparing = setting("order.notification.message.preparing", "");
        $this->ready = setting("order.notification.message.ready", "");
        $this->enroute = setting("order.notification.message.enroute", "");
        $this->completed = setting("order.notification.message.completed", "");
        $this->cancelled = setting("order.notification.message.cancelled", "");
        $this->failed = setting("order.notification.message.failed", "");
        $this->managerPreparingMssage = setting("order.notification.message.manager.preparing", "");
        $this->managerEnrouteMsg = setting("order.notification.message.manager.enroute", "");
        $this->showCustomNotificationMessage = true;
    }

    public function saveCustomNotificationSettings()
    {

        try {

            $this->isDemo();
            setting([
                'order.notification.message.pending' =>  $this->pending,
                'order.notification.message.preparing' =>  $this->preparing,
                'order.notification.message.ready' =>  $this->ready,
                'order.notification.message.enroute' =>  $this->enroute,
                'order.notification.message.completed' =>  $this->completed,
                'order.notification.message.cancelled' =>  $this->cancelled,
                'order.notification.message.failed' =>  $this->failed,
                'order.notification.message.manager.preparing' =>  $this->managerPreparingMssage,
                'order.notification.message.manager.enroute' =>  $this->managerEnrouteMsg,
            ])->save();
            $this->showSuccessAlert(__("Custom Notification Message Setting saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Custom Notification Message Setting Failed"));
        }
    }



    //Meeeting settings
    public function privacySettings()
    {
        $this->privacyPolicy = setting('privacyPolicy', "");
        $this->showPrivacy = true;
        // $this->dispatchBrowserEvent('privacyPolicyChange', ["value" => $this->privacyPolicy]);
    }

    public function savePrivacyPolicy()
    {

        try {

            $this->isDemo();
            $converter = new CommonMarkConverter([
                'html_input' => 'unstrip',
                'allow_unsafe_links' => false,
            ]);

            $this->privacyPolicy = $converter->convertToHtml($this->privacyPolicy);
            setting([
                'privacyPolicy' =>  $this->privacyPolicy,
            ])->save();

            $this->showSuccessAlert(__("Privacy Policy Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Privacy Policy Settings save failed!"));
            // $this->showErrorAlert("Privacy Policy ===> " . $this->privacyPolicy . "");
        }
    }


    //
    public function contactSettings()
    {
        $this->contactInfo = setting('contactInfo', "");
        $this->showContact = true;
    }

    public function saveContactInfo()
    {

        try {

            $this->isDemo();

            setting([
                'contactInfo' =>  $this->contactInfo,
            ])->save();

            $this->showSuccessAlert(__("Contact Info saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Contact Info save failed!"));
        }
    }


    public function termsSettings()
    {
        $this->terms = setting('terms', "");
        $this->showTerms = true;
    }

    public function saveTermsSettings()
    {

        try {

            $this->isDemo();

            setting([
                'terms' =>  $this->terms,
            ])->save();

            $this->showSuccessAlert(__("Terms & conditions saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Terms & conditions save failed!"));
        }
    }



    //PAGE SETTINGS
    public function pageSettings()
    {
        $this->driverDocumentInstructions = setting('page.settings.driverDocumentInstructions', "");
        $this->vendorDocumentInstructions = setting('page.settings.vendorDocumentInstructions', "");
        $this->showPageSetting = true;
    }

    public function savePageSettings()
    {

        try {

            $this->isDemo();

            setting([
                'page.settings.driverDocumentInstructions' =>  $this->driverDocumentInstructions,
                'page.settings.vendorDocumentInstructions' =>  $this->vendorDocumentInstructions,
            ])->save();

            $this->showSuccessAlert(__("Page Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Page Settings save failed!"));
        }
    }




    //
    function fileEditContents($file_name, $line, $new_value)
    {
        $file = explode("\n", rtrim(file_get_contents($file_name)));
        $file[$line] = $new_value;
        $file = implode("\n", $file);
        file_put_contents($file_name, $file);
    }
}
