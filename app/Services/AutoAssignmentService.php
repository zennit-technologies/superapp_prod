<?php

namespace App\Services;

use App\Traits\FirebaseMessagingTrait;

class AutoAssignmentService
{
    use FirebaseMessagingTrait;
    public function __constuct()
    {
        //
    }

    public function sendNewOrderNotification($driver, $newOrderData, $address, $distance)
    {
        $driverTopic = "d_" . $driver->id . "";
        $title = __("New Order Alert");
        $body = __("Pickup Location") . ": " . $address . " (" . $distance . "km)";
        $newOrderData["title"] = $title;
        $newOrderData["body"] = $body;
        $this->sendFirebaseNotification($driverTopic, $title, $body, $newOrderData, $onlyData = true, "new_order_channel", $noSound = true);
    }

    public function sendFailedNewOrderNotification($driver, $autoAssignment)
    {
        $driverTopic = "d_" . $driver->id . "";
        $title = "#" . $autoAssignment->order->code . " " . __("Order Alert(Released)");
        $body = __("This order has not receive update from you and its there be released for other driver to accept");
        $notificationData = [
            "title" => $title,
            "body" => $body,
        ];
        $this->sendFirebaseNotification($driverTopic, $title, $body, $notificationData, $onlyData = true, "new_order_channel", $noSound = true);
    }
}
