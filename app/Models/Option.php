<?php

namespace App\Models;

class Option extends BaseModel
{

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function option_group()
    {
        return $this->belongsTo('App\Models\OptionGroup', 'option_group_id', 'id');
    }

}
