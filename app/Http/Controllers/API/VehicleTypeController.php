<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TaxiCurrencyPricing;
use App\Models\VehicleType;
use App\Traits\GoogleMapApiTrait;
use App\Traits\TaxiTrait;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{

    use GoogleMapApiTrait, TaxiTrait;
    public function index()
    {
        return VehicleType::active()->get();
    }


    //using this to 
    public function calculateFee(Request $request)
    {

        //
        $vehicleTypes = VehicleType::active()->get();

        //check if multiple currency is enabled
        $multipleCurrency = (bool) setting('taxi.multipleCurrency', false);
        if ($multipleCurrency && $request->country_code) {
            //
            $taxiCurrencyVehicleTypes = TaxiCurrencyPricing::whereHas('currency', function ($query) use ($request) {
                return $query->where('country_code', $request->country_code);
            })->get();


            //
            $vehicleTypes = [];
            foreach ($taxiCurrencyVehicleTypes as $taxiCurrencyVehicleType) {
                $vehicleType = $taxiCurrencyVehicleType->vehicle_type;
                $vehicleType->base_fare = $taxiCurrencyVehicleType->base_fare;
                $vehicleType->distance_fare = $taxiCurrencyVehicleType->distance_fare;
                $vehicleType->time_fare = $taxiCurrencyVehicleType->time_fare;
                $vehicleType->min_fare = $taxiCurrencyVehicleType->min_fare;
                $vehicleType->currency = $taxiCurrencyVehicleType->currency;
                $vehicleType->total = $this->getTaxiOrderTotalPrice($vehicleType, $request->pickup, $request->dropoff);
                $vehicleType->encrypted = \Crypt::encrypt($vehicleType);
                $vehicleTypes[] = $vehicleType;
            }

            return $vehicleTypes;
        } else {
            //
            $vehicleTypes = $vehicleTypes->map(function ($vehicleType, $key) use ($request) {
                $vehicleType->total = $this->getTaxiOrderTotalPrice($vehicleType, $request->pickup, $request->dropoff);
                return $vehicleType;
            });
        }
        return $vehicleTypes;
    }
}
