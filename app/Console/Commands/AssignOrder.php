<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;
use AnthonyMartin\GeoLocation\GeoPoint;
use App\Models\AutoAssignment;
use App\Services\AutoAssignmentService;
use App\Services\FirestoreRestService;
use App\Traits\FirebaseAuthTrait;

class AssignOrder extends Command
{

    use FirebaseAuthTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find driver to assign order to';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        //get orders in ready state
        $orders = Order::currentStatus('ready')
            ->whereHas('vendor', function ($query) {
                return $query->where('auto_assignment', 1);
            })
            ->doesntHave("auto_assignment")
            ->where(function ($query) {
                $query->whereNotNull('delivery_address_id')
                    ->orWhereHas('stops');
            })
            ->whereNull('driver_id')
            ->limit(20)->get();
        // logger("orders", [$orders->pluck('id')]);

        //
        foreach ($orders as $order) {
            // logger("Order loaded ==> ".$order->code."");
            //

            try {
                $pickupLocationLat = $order->type != "parcel" ? $order->vendor->latitude : $order->pickup_location->latitude;
                $pickupLocationLng = $order->type != "parcel" ? $order->vendor->longitude : $order->pickup_location->longitude;
                //earth distance of order
                $geopointA = new GeoPoint($pickupLocationLat, $pickupLocationLng);
                $geopointB = new GeoPoint(0.00, 0.00);
                $earthDistance = $geopointA->distanceTo($geopointB, 'kilometers');

                //
                $maxOnOrderForDriver = setting('maxDriverOrderAtOnce', 1);
                $driverSearchRadius = setting('driverSearchRadius', 10);
                $earthDistanceToNorth = $earthDistance + $driverSearchRadius;
                $earthDistanceToSouth = $earthDistance - $driverSearchRadius;
                //
                $rejectedDriversCount = AutoAssignment::where(
                    'order_id',
                    $order->id,
                )->count();
                //find driver within that range
                $firestoreRestService = new FirestoreRestService();
                $driverDocuments = $firestoreRestService->whereBetween($earthDistanceToNorth, $earthDistanceToSouth, $rejectedDriversCount);
                // logger("Drivers found for order =>".$order->code."", [$driverDocuments]);

                //
                foreach ($driverDocuments as $driverData) {

                    //found closet driver
                    $driver = User::where('id', $driverData["id"])->first();
                    if (empty($driver)) {
                        continue;
                    }
                    //check if he/she has a pending auto-assignment
                    $anyPendingAutoAssignment = AutoAssignment::where([
                        'driver_id' => $driver->id,
                        'status' => "pending",
                    ])->first();

                    if (!empty($anyPendingAutoAssignment)) {
                        // logger("there is pending auto assign");
                        continue;
                    }

                    //check if he/she has a pending auto-assignment
                    $rejectedThisOrderAutoAssignment = AutoAssignment::where([
                        'driver_id' => $driver->id,
                        'order_id' => $order->id,
                        'status' => "rejected",
                    ])->first();

                    if (!empty($rejectedThisOrderAutoAssignment)) {
                        // logger("".$driver->name." => rejected this order => ".$order->code."");
                        continue;
                    } else {
                        // logger("".$driver->name." => is being notified about this order => ".$order->code."");
                    }

                    // logger("Drivers data", [$driver->is_active, $driver->is_online, $maxOnOrderForDriver, $driver->assigned_orders]);

                    if ($driver->is_active && $driver->is_online && ((int)$maxOnOrderForDriver > $driver->assigned_orders)) {

                        //assign order to him/her
                        $autoAssignment = new AutoAssignment();
                        $autoAssignment->order_id = $order->id;
                        $autoAssignment->driver_id = $driver->id;
                        $autoAssignment->save();

                        //add the new order to it
                        $pickupLocationLat = $order->type != "parcel" ? $order->vendor->latitude : $order->pickup_location->latitude;
                        $pickupLocationLng = $order->type != "parcel" ? $order->vendor->longitude : $order->pickup_location->longitude;
                        $driverDistanceToPickup = $this->getDistance(
                            [
                                $pickupLocationLat,
                                $pickupLocationLng
                            ],
                            [
                                $driverData["lat"],
                                $driverData["long"],
                            ]
                        );
                        $pickup = [
                            'lat' => $pickupLocationLat,
                            'long' => $pickupLocationLng,
                            'address' => $order->type != "parcel" ? $order->vendor->address : $order->pickup_location->address,
                            'city' => $order->type != "parcel" ? "" : $order->pickup_location->city,
                            'state' => $order->type != "parcel" ? "" : $order->pickup_location->state ?? "",
                            'country' => $order->type != "parcel" ? "" : $order->pickup_location->country ?? "",
                            "distance" => number_format($driverDistanceToPickup, 2),
                        ];


                        //dropoff data
                        $dropoffLocationLat = $order->type != "parcel" ? $order->delivery_address->latitude : $order->dropoff_location->latitude;
                        $dropoffLocationLng = $order->type != "parcel" ? $order->delivery_address->longitude : $order->dropoff_location->longitude;
                        $driverDistanceToDropoff = $this->getDistance(
                            [
                                $dropoffLocationLat,
                                $dropoffLocationLng
                            ],
                            [
                                $driverData["lat"],
                                $driverData["long"],
                            ]
                        );

                        $dropoff = [
                            'lat' => $dropoffLocationLat,
                            'long' => $dropoffLocationLng,
                            'address' => $order->type != "parcel" ? $order->delivery_address->address : $order->dropoff_location->address,
                            'city' => $order->type != "parcel" ? "" : $order->dropoff_location->city,
                            'state' => $order->type != "parcel" ? "" : $order->pickup_location->state ?? "",
                            'country' => $order->type != "parcel" ? "" : $order->pickup_location->country ?? "",
                            "distance" => number_format($driverDistanceToDropoff, 2),
                        ];
                        //
                        $newOrderData = [
                            "pickup" => json_encode($pickup),
                            "dropoff" => json_encode($dropoff),
                            'amount' => (string)$order->delivery_fee,
                            'total' => (string)$order->total,
                            'vendor_id' => (string)$order->vendor_id,
                            'is_parcel' => (string)($order->type == "parcel"),
                            'package_type' =>  (string)($order->package_type->name ?? ""),
                            'id' => (string)$order->id,
                            'range' => (string)$order->vendor->delivery_range,
                        ];
                        //send the new order to driver via push notification
                        $autoAssignmentSerivce = new AutoAssignmentService();
                        $autoAssignmentSerivce->sendNewOrderNotification($driver, $newOrderData, $pickup["address"], $driverDistanceToPickup);
                    }
                }
            } catch (\Exception $ex) {
                // logger("Skipping Order", [$order->id]);
                // logger("Order Error", [$ex->getMessage() ?? '']);
            }
        }
    }


    //
    public function getDistance($loc1, $loc2)
    {
        $geopointA = new GeoPoint($loc1[0], $loc1[1]);
        $geopointB = new GeoPoint($loc2[0], $loc2[1]);
        return $geopointA->distanceTo($geopointB, 'kilometers');
    }
}
