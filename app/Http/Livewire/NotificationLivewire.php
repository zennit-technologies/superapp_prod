<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\PushNotification;
use App\Models\Vendor;
use App\Traits\FirebaseMessagingTrait;
use Exception;
use Spatie\Permission\Models\Role;

class NotificationLivewire extends BaseLivewireComponent
{
    use FirebaseMessagingTrait;

    public $headings;
    public $message;
    public $roles;
    public $imageUrl;

    //
    public $allReceiver;
    public $customReceiver;
    public $customReceiverRoles;

    //
    public $useProduct = false;
    public $useVendor = false;
    public $useService = false;

    //
    public $vendor_id;
    public $product_id;
    public $service_id;


    protected $rules = [
        "headings" => "required|string",
        "message" => "required|string",
    ];


    public function getListeners()
    {
        return $this->listeners + [
            'autocompleteServiceSelected' => 'autocompleteServiceSelected',
        ];
    }

    public function mount()
    {
        $this->allReceiver = true;
        $this->customReceiver = false;
        $this->customReceiverRoles = [];
    }

    public function render()
    {

        $this->roles = Role::all();
        return view('livewire.notification');
    }

    public function updatedAllReceiver()
    {
        $this->customReceiver = !$this->allReceiver;
    }
    public function updatedCustomReceiver()
    {
        $this->allReceiver = !$this->customReceiver;
    }



    public function sendNotification()
    {

        $this->validate();

        try {

            $this->isDemo();

            //
            if ($this->customReceiver) {
                //
                $notificationTopic = implode(",", $this->customReceiverRoles);
            }
            \DB::beginTransaction();
            $pushNotification = new PushNotification();
            $pushNotification->title = $this->headings;
            $pushNotification->body = $this->message;
            $pushNotification->role = $notificationTopic ?? "all";
            $pushNotification->user_id = \Auth::id();
            $pushNotification->vendor_id = $this->vendor_id;
            $pushNotification->product_id = $this->product_id;
            $pushNotification->service_id = $this->service_id;
            $pushNotification->save();


            //upload image if selected
            if ($this->photo) {
                $pushNotification->clearMediaCollection();
                $pushNotification->addMedia($this->photo->getRealPath())->toMediaCollection();
                $this->photo = null;

                //
                $this->imageUrl = $pushNotification->photo;
            }


            //
            $notificationData = [
                "title" => $this->headings,
                "body" => $this->message,
            ];

            //image
            if(!empty($this->imageUrl)){
                $notificationData["image"] = $this->imageUrl;
            }

            //vendor
            if(!empty($pushNotification->vendor) && $this->useVendor){
                $notificationData["vendor"] = json_encode($pushNotification->vendor->toArray());
            }
            //product
            if(!empty($pushNotification->product) && $this->useProduct){
                $notificationData["product"] = json_encode($pushNotification->product->toArray());
            }
            //service
            if(!empty($pushNotification->service) && $this->useService){
                $notificationData["service"] = json_encode($pushNotification->service->toArray());
            }
            //
            $notificationTopic = "all";
            //fetching topic to send message to
            if ($this->customReceiver) {

                foreach ($this->customReceiverRoles as $topic) {
                    $this->sendFirebaseNotification(
                        $topic,
                        $this->headings,
                        $this->message,
                        $notificationData,
                        $onlyData = false,
                        $channel_id = "basic_channel",
                        $noSound = false,
                        $image = $this->imageUrl
                    );
                }
            } else {
                $this->sendFirebaseNotification(
                    "all",
                    $this->headings,
                    $this->message,
                    $notificationData,
                    $onlyData = false,
                    $channel_id = "basic_channel",
                    $noSound = false,
                    $image = $this->imageUrl
                );
            }


            \DB::commit();
            $this->showSuccessAlert(__("Notification sent successful"));
            $this->reset();
            $this->mount();
            $this->emit('refreshTable');
        } catch (Exception $error) {
            \DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Notification failed"));
        }
    }



    ///
    public function UpdatedUseProduct($value)
    {
        if ($value) {
            $this->useVendor = false;
            $this->useService = false;
        }
    }

    public function UpdatedUseVendor($value)
    {
        if ($value) {
            $this->useProduct = false;
            $this->useService = false;
        }
    }

    public function UpdatedUseService($value)
    {
        if ($value) {
            $this->useProduct = false;
            $this->useVendor = false;
        }
    }

    public function autocompleteVendorSelected($vendor)
    {
        $this->vendor_id = $vendor["id"];
    }

    public function autocompleteProductSelected($product)
    {
        $this->product_id = $product["id"];
    }

    public function autocompleteServiceSelected($service)
    {
        $this->service_id = $service["id"];
    }
}
