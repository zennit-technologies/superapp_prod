<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Kirschbaum\PowerJoins\PowerJoins;


class Product extends BaseModel
{
    use PowerJoins;
    protected $fillable = [
        "id", "name", "description", "price", "discount_price", "capacity", "unit", "package_count", "available_qty", "featured", "deliverable", "is_active", "vendor_id"
    ];

    protected $appends = ['formatted_date', 'photo', 'is_favourite', 'option_groups', 'photos'];
    protected $with = ['vendor'];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->whereHas('vendor', function ($q) {
            $q->where('is_active', 1);
        });
    }

    public function scopeMine($query)
    {

        return $query->when(Auth::user()->hasRole('manager'), function ($query) {
            return $query->where('vendor_id', Auth::user()->vendor_id);
        })->when(Auth::user()->hasRole('city-admin'), function ($query) {
            return $query->where('creator_id', Auth::id());
        });
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendor_id', 'id')->withTrashed();
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function sub_categories()
    {
        return $this->belongsToMany('App\Models\Subcategory');
    }

    public function menus()
    {
        return $this->belongsToMany('App\Models\Menu');
    }

    public function options()
    {
        return $this->belongsToMany('App\Models\Option');
    }

    public function option_groups()
    {
        // return $this->hasManyThrough(
        //     OptionGroup::class,
        //     ProductOption::class,
        //     'product_id', // Foreign key on the Option table...
        //     'id', // Foreign key on the OptionGroup table...
        //     'id', // Local key on the product table...
        //     'option_group_id' // Local key on the Option table...
        // )->groupBy('id');
    }

    public function getOptionGroupsAttribute()
    {

        $optionGroupIds = Option::whereHas('products', function ($query) {
            return $query->where('product_id', "=", $this->id);
        })->pluck('option_group_id');

        //
        return OptionGroup::with(['options' => function ($query) {
            $query->whereHas('products', function ($query) {
                return $query->where('product_id', "=", $this->id);
            });
        }])->whereIn('id', $optionGroupIds)->get();
    }

    public function sales()
    {
        return $this->hasMany('App\Models\OrderProduct', 'product_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\OrderProduct')->whereHas(
            "order",
            function ($query) {
                return $query->where("user_id",  auth('sanctum')->user()->id);
            }
        );
    }

    public function getIsFavouriteAttribute()
    {

        if (auth('sanctum')->user()) {
            return $this->favourite ? true : false;
        } else {
            return false;
        }
    }

    public function favourite()
    {
        return $this->belongsTo('App\Models\Favourite', 'id', 'product_id')->where("user_id", "=", auth('sanctum')->user()->id);
    }


    public function getPhotosAttribute()
    {
        $mediaItems = $this->getMedia('default');
        $photos = [];

        foreach ($mediaItems as $mediaItem) {
            array_push($photos, $mediaItem->getFullUrl());
        }
        return $photos;
    }
}
