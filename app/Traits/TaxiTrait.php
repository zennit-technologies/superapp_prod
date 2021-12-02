<?php

namespace App\Traits;


use Illuminate\Support\Facades\Http;

trait TaxiTrait
{
    use GoogleMapApiTrait;

    public function getTaxiOrderTotalPrice($vehicleType,$pickup,$dropoff)
    {

        //distance of trip
        $distance = $this->getRelativeDistance($pickup, $dropoff);
        $drivingSpeed = setting("taxi.drivingSpeed", 50);
        //calculate the driving time and convert to minutes from hours
        $drivingTime = ($distance / $drivingSpeed) * 60;

        $timeFare = $vehicleType->time_fare * $drivingTime;
        $distanceFare = $distance * $vehicleType->distance_fare;
        $totalTripFare = $vehicleType->base_fare + $timeFare + $distanceFare;
        //if the total amount is less than the set minimum fare by admin, then the min_fare should be used then
        if($totalTripFare < $vehicleType->min_fare){
            return $vehicleType->min_fare;
        }else{
            return  $totalTripFare;
        }
    }

    
}
