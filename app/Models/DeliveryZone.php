<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kirschbaum\PowerJoins\PowerJoins;

class DeliveryZone extends NoDeleteBaseModel
{
    use HasFactory;
    use PowerJoins;

    
    public function points()
    {
        return $this->hasMany('App\Models\DeliveryZonePoint');
    }

    public function vendors()
    {
        return $this->hasMany('App\Models\Vendor');
    }
}
