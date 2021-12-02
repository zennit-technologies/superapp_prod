<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends BaseModel
{
 
    protected $appends = ['formatted_expires_on','use_left','expired'];

    public function getFormattedExpiresOnAttribute(){
        return Carbon::parse($this->expires_on)->format('d M Y');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }

    public function vendors()
    {
        return $this->belongsToMany('App\Models\Vendor');
    }

    public function getUseLeftAttribute()
    {

        if(empty($this->times)){
            return 1;
        }
        
        $couponUses = CouponUser::where([
            'coupon_id' => $this->id,
            'user_id' => auth('api')->user()->id,
        ])->get()->count();
        //
        return $this->times - $couponUses;
    }

    public function getExpiredAttribute()
    {

        return $this->expires_on < now();
    }
}
