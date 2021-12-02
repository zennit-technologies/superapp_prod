<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\User;
use App\Models\UserToken;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\WebPushConfig;

trait FirebaseMessagingTrait
{

    use FirebaseAuthTrait;


    //
    private function sendFirebaseNotification(
        $topic,
        $title,
        $body,
        array $data = null,
        bool $onlyData = true,
        string $channel_id = "basic_channel",
        bool $noSound = false,
        String $image = null
    ) {

        //getting firebase messaging
        $messaging = $this->getFirebaseMessaging();
        $messagePayload = [
            'topic' => (string) $topic,
            'notification' => $onlyData ? null : [
                'title' => $title,
                'body' => $body,
                'image' => $image,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                "channel_id" => $channel_id,
                "sound" => $noSound ? "" : "alert.aiff",
            ],
            'data' => $data,
        ];

        if (!$onlyData) {
            $messagePayload = [
                'topic' => (string) $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'image' => $image,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    "channel_id" => $channel_id,
                    "sound" => $noSound ? "" : "alert.aiff",
                ],
            ];
        } else {

            if (empty($data["title"])) {
                $data["title"] = $title;
                $data["body"] = $body;
            }
            $messagePayload = [
                'topic' => (string) $topic,
                'data' => $data,
            ];
        }
        $message = CloudMessage::fromArray($messagePayload);

        //android configuration
        $androidConfig = [
            'ttl' => '3600s',
            'priority' => 'high',
            'data' => $data,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'image' => $image,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                "channel_id" => $channel_id,
                "sound" => $noSound ? "" : "alert",
            ],
        ];

        if ($onlyData) {
            if (empty($data["title"])) {
                $data["title"] = $title;
                $data["body"] = $body;
            }
            $androidConfig = [
                'ttl' => '3600s',
                'priority' => 'high',
                'data' => $data,
            ];
        }
        $config = AndroidConfig::fromArray($androidConfig);
        $message = $message->withAndroidConfig($config);
        $messaging->send($message);
    }

    private function sendFirebaseNotificationToTokens(array $tokens, $title, $body, array $data = null)
    {
        if (!empty($tokens)) {
            //getting firebase messaging
            $messaging = $this->getFirebaseMessaging();
            $message = CloudMessage::new();
            //
            $config = WebPushConfig::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => setting('websiteLogo', asset('images/logo.png')),
                ],
                'fcm_options' => [
                    'link' => $data[0],
                ],
            ]);
            //
            $message = $message->withWebPushConfig($config);
            $messaging->sendMulticast($message, $tokens);
        }
    }










    //
    public function sendOrderStatusChangeNotification(Order $order)
    {


        //order data
        $orderData = [
            'is_order' => "1",
            'order_id' => (string)$order->id,
        ];
        //for taxi orders
        if (!empty($order->taxi_order) || empty($order->vendor)) {
            $this->sendTaxiOrderStatusChangeNotification($order);
            return;
        }
        //
        $managersId = $order->vendor->managers->pluck('id')->all() ?? [];
        $managersTokens = UserToken::whereIn('user_id', $managersId)->pluck('token')->toArray();


        //
        $pendingMsg = setting('order.notification.message.pending', __("Your order is pending"));
        $preparingMsg = setting('order.notification.message.preparing', __("Your order is now being prepared"));
        $readyMsg = setting('order.notification.message.ready', __("Your order is now ready for delivery/pickup"));
        $enrouteMsg = setting(
            'order.notification.message.enroute',
            __("Order #") . $order->code . __(" has been assigned to a delivery boy")
        );
        $completedMsg = setting('order.notification.message.completed',  __("Order #") . $order->code . __(" has been delivered"));
        $cancelledMsg = setting('order.notification.message.cancelled', __("Order #") . $order->code . " " . __("cancelled"));
        $failedMsg = setting('order.notification.message.failed', __("Trip failed"));
        //managers message 
        $managerPendingMsg = setting(
            'order.notification.message.manager.preparing',
            __("Order #") . $order->code . __(" has just been placed with you")
        );
        $managerEnrouteMsg = setting(
            'order.notification.message.manager.enroute',
            __("Order #") . $order->code . __(" has been assigned to a delivery boy")
        );
        $notificationTitle = setting('websiteName', env("APP_NAME"));


        //'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if ($order->status == "pending") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $pendingMsg, $orderData);
            //web
            $this->sendFirebaseNotificationToTokens($managersTokens, $notificationTitle, $managerPendingMsg, [route('orders')]);
            //vendor
            $this->sendFirebaseNotification("v_" . $order->vendor_id, $notificationTitle, $managerPendingMsg, $orderData);
        } else if ($order->status == "preparing") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $preparingMsg, $orderData);
        } else if ($order->status == "ready") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $readyMsg, $orderData);
        } else if ($order->status == "enroute") {

            //web
            $this->sendFirebaseNotificationToTokens($managersTokens, $notificationTitle, $managerEnrouteMsg, [route('orders')]);
            //user
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $enrouteMsg, $orderData);
            //vendor
            $this->sendFirebaseNotification("v_" . $order->vendor_id, $notificationTitle, $managerEnrouteMsg, $orderData);
        } else if ($order->status == "delivered") {
            //user/customer
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $completedMsg, $orderData);
            //vendor
            $this->sendFirebaseNotification("v_" . $order->vendor_id, $notificationTitle, __("Order #") . $order->code . __(" has been delivered"), $orderData);

            //driver
            if (!empty($order->driver_id)) {
                $this->sendFirebaseNotification(
                    $order->driver_id,
                    $notificationTitle,
                    $completedMsg,
                    $orderData
                );
            }
        } else if ($order->status == "failed") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $failedMsg, $orderData);
        } else if ($order->status == "cancelled") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $cancelledMsg, $orderData);
        } else if (!empty($order->status)) {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, __("Order #") . $order->code . __(" has been ") . __($order->status) . "", $orderData);
        }


        //send notifications to admin & city-admin
        //admin 
        if (setting("notifyAdmin", 0)) {
            //sending notification to admin accounts
            $adminsIds = User::admin()->pluck('id')->all();
            $adminTokens = UserToken::whereIn('user_id', $adminsIds)->pluck('token')->toArray();
            //
            $this->sendFirebaseNotificationToTokens(
                $adminTokens,
                __("Order Notification"),
                __("Order #") . $order->code . " " . __("with") . " " . $order->vendor->name . " " . __("is now:") . " " . $order->status,
                [route('orders')]
            );
        }
        //city-admin 
        if (setting("notifyCityAdmin", 0) && !empty($order->vendor->creator_id)) {
            //sending notification to city-admin accounts
            $cityAdminTokens = UserToken::where('user_id', $order->vendor->creator_id)->pluck('token')->toArray();
            //
            $this->sendFirebaseNotificationToTokens(
                $cityAdminTokens,
                __("Order Notification"),
                __("Order #") . $order->code . " " . __("with") . " " . $order->vendor->name . " " . __("is now:") . " " . $order->status,
                [route('orders')]
            );
        }
    }

    //
    public function sendTaxiOrderStatusChangeNotification(Order $order)
    {


        //order data
        $orderData = [
            'is_order' => "1",
            'order_id' => (string)$order->id,
        ];

        $pendingMsg = setting('taxi.msg.pending', __("Searching for driver"));
        $preparingMsg = setting('taxi.msg.preparing', __("Driver assigned to your trip and their way"));
        $readyMsg = setting('taxi.msg.ready', __("Driver has arrived"));
        $enrouteMsg = setting('taxi.msg.enroute', __("Trip started"));
        $completedMsg = setting('taxi.msg.completed', __("Trip completed"));
        $cancelledMsg = setting('taxi.msg.cancelled', __("Trip was cancelled"));
        $failedMsg = setting('taxi.msg.failed', __("Trip failed"));
        $notificationTitle = setting('websiteName', env("APP_NAME"));

        //'pending','preparing','ready','enroute','delivered','failed','cancelled'
        if ($order->status == "pending") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $pendingMsg, $orderData);
        } else if ($order->status == "preparing") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $preparingMsg, $orderData);
        } else if ($order->status == "ready") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $readyMsg, $orderData);
        } else if ($order->status == "enroute") {

            //user
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $enrouteMsg, $orderData);
        } else if ($order->status == "delivered") {
            //user/customer
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $completedMsg, $orderData);

            //driver
            if (!empty($order->driver_id)) {
                $this->sendFirebaseNotification(
                    $order->driver_id,
                    $notificationTitle,
                    $completedMsg,
                    $orderData
                );
            }
        } else if ($order->status == "failed") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $failedMsg, $orderData);
        } else if ($order->status == "cancelled") {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, $cancelledMsg, $orderData);
        } else if (!empty($order->status)) {
            $this->sendFirebaseNotification($order->user_id, $notificationTitle, __("Trip #") . $order->code . __(" has been ") . __($order->status) . "", $orderData);
        }


        //send notifications to admin & city-admin
        //admin 
        if (setting("notifyAdmin", 0)) {
            //sending notification to admin accounts
            $adminsIds = User::admin()->pluck('id')->all();
            $adminTokens = UserToken::whereIn('user_id', $adminsIds)->pluck('token')->toArray();
            //
            $this->sendFirebaseNotificationToTokens(
                $adminTokens,
                __("Trip Notification"),
                __("Trip #") . $order->code . " " . __("by") . " " . $order->user->name . " " . __("is now:") . " " . $order->status,
                [route('orders')]
            );
        }
    }


    public function sendOrderNotificationToDriver(Order $order)
    {


        //order data
        $orderData = [
            'is_order' => "1",
            'order_id' => (string)$order->id,
        ];

        //
        $this->sendFirebaseNotification(
            $order->driver_id,
            __("Order Update"),
            __("Order #") . $order->code . __(" has been assigned to you"),
            $orderData
        );
    }

    //notificat chat parties
    public function sendChatNotification(Order $order)
    {
        // //chat sample
        // $this->sendFirebaseNotification($topic, $this->headings, $this->message, [
        //     'is_chat' => "1",
        //     'code' => "hfjh27hj",
        //     'vendor' => json_encode([
        //         "id" => 1,
        //         "name" => "Meme Inc.",
        //         "photo" => "https://img.icons8.com/cute-clipart/344/apple-app-store.png",
        //     ]),
        //     'user' => json_encode([
        //         "id" => 6,
        //         "name" => "Client User",
        //         "photo" => "https://img.icons8.com/cute-clipart/344/apple-app-store.png",
        //     ]),
        // ]);
    }
}
