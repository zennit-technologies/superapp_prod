<?php

namespace App\Traits;


use Illuminate\Support\Facades\Http;
use AnthonyMartin\GeoLocation\GeoPoint;

trait GoogleMapApiTrait
{


    public function getTotalDistanceFromGoogle($originLocation, $destinationLocations)
    {

        $googleMapDistanceResposne = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            "key" => setting("googleMapKey", ""),
            "origins" => $originLocation,
            "destinations" => $destinationLocations,
        ]);

        if ($googleMapDistanceResposne->successful()) {
            $distance = 0;
            $distanceElements = $googleMapDistanceResposne->json()["rows"][0]["elements"];

            foreach ($distanceElements as $distanceElement) {
                $distance += $distanceElement["distance"]["value"];
            }

            return $distance / 1000;
        } else {
            throw new Exception(__("An error occured on our server"), 1);
        }
    }

    public function getLinearDistance($originLocation, $destinationLocations)
    {
        $lat1 = explode(",", $originLocation)[0];
        $lon1 = explode(",", $originLocation)[1];
        //
        $lat2 = explode(",", $destinationLocations)[0];
        $lon2 = explode(",", $destinationLocations)[1];
        // $lat1, $lon1, $lat2, $lon2
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;
        $r = 6372.797; // mean radius of Earth in km 
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;
        return $km;
    }



    //
    public function getRelativeDistance($originLocation, $destinationLocations)
    {
        try {
            if (setting('enableGoogleDistance', 0)) {
                $distance = $this->getTotalDistanceFromGoogle($originLocation, $destinationLocations);
            } else {
                $distance = $this->getLinearDistance($originLocation, $destinationLocations);
            }
        } catch (\Exception $ex) {
            $distance = $this->getLinearDistance($originLocation, $destinationLocations);
        }

        //
        return $distance;
    }

    public function getEarthDistance($lat, $lng)
    {
        $geopointA = new GeoPoint($lat, $lng);
        $geopointB = new GeoPoint(0.00, 0.00);
        return $geopointA->distanceTo($geopointB, 'kilometers');
    }


    function insideBound($point, $fenceArea)
    {

        $x = $point['lat'];
        $y = $point['lng'];

        $inside = false;
        for ($i = 0, $j = count($fenceArea) - 1; $i <  count($fenceArea); $j = $i++) {
            $xi = $fenceArea[$i]['lat'];
            $yi = $fenceArea[$i]['lng'];
            $xj = $fenceArea[$j]['lat'];
            $yj = $fenceArea[$j]['lng'];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }
}
