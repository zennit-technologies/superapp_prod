<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use App\Models\Vendor;
use App\Traits\GoogleMapApiTrait;
use Illuminate\Http\Request;


class RegularOrderController extends Controller
{
    use GoogleMapApiTrait;
    //
    public function summary(Request $request)
    {


        //delivery_address_id
        //vendor_id

        //
        $vendor = Vendor::find($request->vendor_id);

       //
        if (setting('enableGoogleDistance', 0)) {

            //
            $deliveryAddressLocation = $this->getDeliveryAddress($request->delivery_address_id);
            $destinationLatLngs = "" . $deliveryAddressLocation->latitude . "," . $deliveryAddressLocation->longitude;
            //
            
            $originLatLng = "" . $vendor->latitude . "," . $vendor->longitude;

            //
            $deliveryLocationDistance = $this->getTotalDistanceFromGoogle(
                $originLatLng,
                $destinationLatLngs
            );

            //


        }else{
            //linear distance calculation
            $deliveryLocationDistance = DeliveryAddress::distance($vendor->latitude, $vendor->longitude)
                ->where('id', $request->delivery_address_id)
                ->first()
                ->distance;

        }


        //calculate the distance price
        if ($vendor->charge_per_km) {
            $distanceAmount = $vendor->delivery_fee * $deliveryLocationDistance;
        } else {
            $distanceAmount = $vendor->delivery_fee;
        }
        //
        $distanceAmount += $vendor->base_delivery_fee;

        return response()->json([
            "delivery_fee" => $distanceAmount,
        ]);
    }



    //
    public function getDeliveryAddress($id): DeliveryAddress
    {
        return DeliveryAddress::find($id);
    }


  
}
