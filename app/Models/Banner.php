<?php

namespace App\Models;

class Banner extends BaseModel
{

    protected $appends = ['formatted_date', 'photo'];
    protected $with = ["category","vendor"];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }
    public function vendor()
    {
        return $this->hasOne('App\Models\Vendor', 'id', 'vendor_id');
    }
}
