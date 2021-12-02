<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use App\Models\Vendor;
use App\Traits\GoogleMapApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryAddressController extends Controller
{

    use GoogleMapApiTrait;


    public function index(Request $request)
    {

        $vendor = Vendor::find($request->vendor_id);

        if ($request->action == "default") {
            //default delivery address
            $deliveryAddresses = DeliveryAddress::where('user_id', "=", Auth::id())->where('is_default', 1)
                ->when($request->vendor_id, function ($query) use ($vendor) {
                    return $query->distance($vendor->latitude, $vendor->longitude);
                })->limit(1)->get();
        } else {

            $deliveryAddresses = DeliveryAddress::where('user_id', "=", Auth::id())
                ->when($request->vendor_id, function ($query) use ($vendor) {
                    return $query->distance($vendor->latitude, $vendor->longitude);
                })->orderBy('updated_at', 'DESC')->get();
        }

        //
        if (!empty($request->vendor_id)) {

            foreach ($deliveryAddresses as $deliveryAddress) {
                $deliveryAddress->can_deliver = $this->locationInZone($vendor, $deliveryAddress);
            }
        }

        if ($request->action == "default") {
            return response()->json($deliveryAddresses->first, 200);
        } else {
            return response()->json([
                "data" => $deliveryAddresses,
                "vendor" => $vendor,
            ], 200);
        }
    }

    public function store(Request $request)
    {

        try {

            $model = new DeliveryAddress();
            $model->fill($request->all());
            $model->user_id = Auth::id();
            $model->save();

            return response()->json([
                "message" => __("Delivery address created successfully"),
                "data" => $model,
            ], 200);
        } catch (\Exception $ex) {

            return response()->json([
                "message" => $ex->getMessage() ?? __("Delivery address creation failed")
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {

        try {

            $model = DeliveryAddress::where('user_id', Auth::id())->where('id', $id)->firstorfail();
            $model->fill($request->all());
            $model->save();

            return response()->json([
                "message" => __("Delivery address updated successfully")
            ], 200);
        } catch (\Exception $ex) {

            return response()->json([
                "message" => __("Delivery address update failed")
            ], 400);
        }
    }

    public function destroy(Request $request, $id)
    {

        try {

            DeliveryAddress::where('user_id', Auth::id())->where('id', $id)->firstorfail()->delete();
            return response()->json([
                "message" => __("Delivery address deleted successfully")
            ], 200);
        } catch (\Exception $ex) {
            logger("Erro", [$ex]);
            return response()->json([
                "message" => __("No Delivery address Found")
            ], 400);
        }
    }




    //
    public function locationInZone($vendor, $deliveryAddress)
    {
        //linear distance check
        if (empty($vendor->delivery_zone)) {
            $originLatLng = "" . $vendor->latitude . "," . $vendor->longitude . "";
            $destinationLatLng = "" . $deliveryAddress->latitude . "," . $deliveryAddress->longitude . "";
            $deliveryAddressDistanceToVendor = $this->getLinearDistance($originLatLng, $destinationLatLng);
            return $deliveryAddressDistanceToVendor < $vendor->delivery_range;
        }
        return $this->insideBound(
            [
                "lat" => $deliveryAddress->latitude,
                "lng" => $deliveryAddress->longitude,
            ],
            $vendor->delivery_zone->points
        );
    }
}
