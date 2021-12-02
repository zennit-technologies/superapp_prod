<?php

namespace App\Models;


class Vehicle extends NoDeleteBaseModel
{

    protected $with = ['driver', 'car_model.car_make', 'vehicle_type'];

    public function driver()
    {
        return $this->belongsTo('App\Models\User', 'driver_id', 'id');
    }

    public function car_model()
    {
        return $this->belongsTo('App\Models\CarModel', 'car_model_id', 'id');
    }

    public function vehicle_type()
    {
        return $this->belongsTo('App\Models\VehicleType', 'vehicle_type_id', 'id');
    }

    public function getPhotosAttribute(){
        $mediaItems = $this->getMedia('default');
        $photos = [];

        foreach ($mediaItems as $mediaItem) {
            array_push($photos, $mediaItem->getFullUrl());
        }
        return $photos;
    }
}
