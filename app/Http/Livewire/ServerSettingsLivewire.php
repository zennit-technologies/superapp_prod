<?php

namespace App\Http\Livewire;

use App\Mail\PlainMail;
use Exception;
use GeoSot\EnvEditor\Facades\EnvEditor;

class ServerSettingsLivewire extends BaseLivewireComponent
{

    // App settings
    public $mailHost;
    public $mailPort;
    public $mailUsername;
    public $mailPassword;
    public $mailFromAddress;
    public $mailFromName;
    public $mailEncryption;
    public $androidDownloadLink;
    public $iosDownloadLink;

    //testing
    public $testEmail;
    public $testSubject;
    public $testBody;


    public function mount()
    {
        //
        $this->mailHost = env('MAIL_HOST');
        $this->mailPort = env('MAIL_PORT');
        $this->mailUsername = env('MAIL_USERNAME');
        $this->mailPassword = env('MAIL_PASSWORD');
        $this->mailFromAddress = env('MAIL_FROM_ADDRESS');
        $this->mailFromName = env('MAIL_FROM_NAME');
        $this->mailEncryption = env('MAIL_ENCRYPTION', 'tls');
        $this->androidDownloadLink = setting('androidDownloadLink');
        $this->iosDownloadLink = setting('iosDownloadLink');
    }

    public function render()
    {

        $this->mount();
        return view('livewire.settings.server-settings');
    }


    public function saveMailSettings()
    {

        $this->validate([
            "mailHost" => "required",
            "mailPort" => "required",
            "mailUsername" => "required",
            "mailPassword" => "required",
            'mailFromAddress' => "required",
            'mailFromName' => "required",
            'mailEncryption' => "required",
        ]);

        try {

            $this->isDemo();
            EnvEditor::editKey("MAIL_HOST", $this->mailHost);
            EnvEditor::editKey("MAIL_PORT", $this->mailPort);
            EnvEditor::editKey("MAIL_USERNAME", "'" . $this->mailUsername . "'");
            EnvEditor::editKey("MAIL_PASSWORD", "'" . $this->mailPassword . "'");
            EnvEditor::editKey("MAIL_ENCRYPTION", "'" . $this->mailEncryption . "'");
            if (EnvEditor::keyExists("MAIL_FROM_ADDRESS")) {
                EnvEditor::editKey("MAIL_FROM_ADDRESS", "'" . $this->mailFromAddress . "'");
            } else {
                EnvEditor::addKey("MAIL_FROM_ADDRESS", "'" . $this->mailFromAddress . "'", ['group' => 'MAIL']);
            }

            if (EnvEditor::keyExists("MAIL_FROM_NAME")) {
                EnvEditor::editKey("MAIL_FROM_NAME", "'" . $this->mailFromName . "'");
            } else {
                EnvEditor::addKey("MAIL_FROM_NAME", "'" . $this->mailFromName . "'", ['group' => 'MAIL']);
            }

            $appSettings = [
                'androidDownloadLink' =>  $this->androidDownloadLink,
                'iosDownloadLink' =>  $this->iosDownloadLink,
            ];
            // update app download links
            setting($appSettings)->save();

            $this->showSuccessAlert(__("Mail Settings saved successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Mail Settings save failed!"));
        }
    }


    public function testMailSettings()
    {

        $this->validate([
            "testEmail" => "required",
            "testSubject" => "required",
            "testBody" => "required",
        ]);

        try {

            \Mail::to($this->testEmail)->send(new PlainMail($this->testSubject, $this->testBody));
            $this->showSuccessAlert(__("Test email sent successfully!"));
            $this->reset();
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("Test email failed!"));
        }
    }
}
