<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;


trait OrderTrait
{
    use GoogleMapApiTrait;

    public function getNewOrderStatus(Request $request)
    {

        $orderDate = Carbon::parse("" . $request->pickup_date . " " . $request->pickup_time . "");
        $hoursDiff = Carbon::now()->diffInHours($orderDate);

        if (!empty($request->pickup_date) && $hoursDiff > setting('minScheduledTime', 2)) {
            return "scheduled";
        } else {
            return "pending";
        }
    }
    
}
