<?php

namespace App\Observers;

use App\Models\Payout;
use App\Traits\FirebaseAuthTrait;

class PayoutObserver
{

  

    public function created(Payout $model)
    {
        if ($model->isDirty('status') && $model->status == "successful") {
            $model->earning->amount -= $model->amount;
            $model->earning->save();
        }
    }

    public function updated(Payout $model)
    {
        if ($model->isDirty('status') && $model->status == "successful") {
            $model->earning->amount -= $model->amount;
            $model->earning->save();
        }
    }
}
