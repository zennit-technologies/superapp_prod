<?php

namespace App\Http\Livewire;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\SmsGateway;
use Aloha\Twilio\Twilio;
use App\Services\OTPService;
use GeoSot\EnvEditor\Facades\EnvEditor;

class SMSGatewayLivewire extends BaseLivewireComponent
{

    //
    public $model = SmsGateway::class;

    //
    public $name;
    public $isActive;

    //
    public $accountId;
    public $token;
    public $fromNumber;
    //
    public $authkey;
    public $sender;
    public $route;
    public $authSecret;
    public $template_id;


    //testing
    public $phoneNumber;
    public $testMessage;


    protected $rules = [
        "name" => "required|string",
    ];


    public function render()
    {
        return view('livewire.sms-gateways');
    }

    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->isActive = $this->selectedModel->is_active;

        //
        if ($this->selectedModel->slug == "twilio") {
            $this->accountId = setting("sms_gateways.twilio.accountId");
            $this->token = setting("sms_gateways.twilio.token");
            $this->fromNumber = setting("sms_gateways.twilio.fromNumber");
        } else if ($this->selectedModel->slug == "msg91") {
            $this->authkey = setting("sms_gateways.msg91.authkey");
            $this->template_id = setting("sms_gateways.msg91.template_id");
            $this->sender = setting("sms_gateways.msg91.sender");
            $this->route = setting("sms_gateways.msg91.route");
        } else if ($this->selectedModel->slug == "gatewayapi") {
            $this->authkey = setting("sms_gateways.gatewayapi.authkey");
            $this->sender = setting("sms_gateways.gatewayapi.sender");
            $this->authSecret = setting("sms_gateways.gatewayapi.authSecret");
            $this->token = setting("sms_gateways.gatewayapi.token");
        } else if ($this->selectedModel->slug == "termii") {
            $this->authkey = setting("sms_gateways.termii.authkey");
            $this->sender = setting("sms_gateways.termii.sender");
        } else if ($this->selectedModel->slug == "africastalking") {
            $this->authkey = setting("sms_gateways.africastalking.authkey");
            $this->token = setting("sms_gateways.africastalking.token");
            $this->sender = setting("sms_gateways.africastalking.sender");
        } else if ($this->selectedModel->slug == "hubtel") {
            $this->authkey = setting("sms_gateways.hubtel.authkey");
            $this->token = setting("sms_gateways.hubtel.token");
            $this->sender = setting("sms_gateways.hubtel.sender");
        }
        $this->emit('showEditModal');
    }

    public function update()
    {
        //validate
        $this->validate();

        try {

            $this->isDemo();
            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->name = $this->name;
            $model->is_active = $this->isActive;
            $model->save();


            //
            if ($this->selectedModel->slug == "twilio") {
                setting([
                    'sms_gateways.twilio.accountId' =>  $this->accountId,
                    'sms_gateways.twilio.token' =>  $this->token,
                    'sms_gateways.twilio.fromNumber' =>  $this->fromNumber,
                ])->save();
            } else if ($this->selectedModel->slug == "msg91") {
                setting([
                    'sms_gateways.msg91.authkey' =>  $this->authkey,
                    'sms_gateways.msg91.template_id' =>  $this->template_id,
                    'sms_gateways.msg91.sender' =>  $this->sender,
                    'sms_gateways.msg91.route' =>  $this->route,
                ])->save();
            } else if ($this->selectedModel->slug == "gatewayapi") {
                //
                setting([
                    'sms_gateways.gatewayapi.authkey' =>  $this->authkey,
                    'sms_gateways.gatewayapi.sender' =>  $this->sender,
                    'sms_gateways.gatewayapi.authSecret' =>  $this->authSecret,
                    'sms_gateways.gatewayapi.token' =>  $this->token,
                ])->save();
            } else if ($this->selectedModel->slug == "termii") {
                //
                setting([
                    'sms_gateways.termii.authkey' =>  $this->authkey,
                    'sms_gateways.termii.sender' =>  $this->sender,
                ])->save();
            } else if ($this->selectedModel->slug == "africastalking") {
                //
                setting([
                    'sms_gateways.africastalking.authkey' =>  $this->authkey,
                    'sms_gateways.africastalking.sender' =>  $this->sender,
                    'sms_gateways.africastalking.token' =>  $this->token,
                ])->save();
            } else if ($this->selectedModel->slug == "hubtel") {
                //
                setting([
                    'sms_gateways.hubtel.authkey' =>  $this->authkey,
                    'sms_gateways.hubtel.token' =>  $this->token,
                    'sms_gateways.hubtel.sender' =>  $this->sender,
                ])->save();
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Sms Gateway") . " " . __('created successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Sms Gateway") . " " . __('creation failed!'));
        }
    }



    public function testSMS()
    {

        if ($this->selectedModel->slug == "twilio") {
            $accountId = setting("sms_gateways.twilio.accountId");
            $token = setting("sms_gateways.twilio.token");
            $fromNumber = setting("sms_gateways.twilio.fromNumber");
            //
            $twilio = new Twilio($accountId, $token, $fromNumber);
            //send sms
            try {
                $twilio->message($this->phoneNumber, $this->testMessage);
                $this->showSuccessAlert("SMS sent successfully");
            } catch (\Exception $ex) {
                $this->showErrorAlert($ex->getMessage() ?? "SMS Failed to send");
            }
            // } else if (in_array($this->selectedModel->slug, ["msg91", "gatewayapi", "termii", ])) {
        } else {

            //send sms
            try {
                $otpService = new OTPService();
                $otpService->sendOTP($this->phoneNumber, $this->testMessage, $gateway = $this->selectedModel->slug);
                $this->showSuccessAlert("SMS sent successfully");
            } catch (\Exception $ex) {
                $this->showErrorAlert($ex->getMessage() ?? "SMS Failed to send");
            }
        }

        //
    }
}
