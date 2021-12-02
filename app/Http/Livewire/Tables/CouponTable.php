<?php

namespace App\Http\Livewire\Tables;

use App\Models\Coupon;
use App\Models\User;
use Kdion4891\LaravelLivewireTables\Column;

class CouponTable extends BaseTableComponent
{

    public $model = Coupon::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        $user = User::find(\Auth::id());
        if ($user->hasRole('admin')) {
            return Coupon::with('products', 'vendors');
        } else {
            return Coupon::with('products', 'vendors')->where('creator_id', $user->id);
        }
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id"),
            Column::make(__('Code'),'code')->searchable()->sortable(),
            Column::make(__('Discount'))->view('components.table.coupon_discount_price')->searchable()->sortable(),
            Column::make(__('Description'))->view('components.table.short_description'),
            Column::make(__('Expires On'),'expires_on')->sortable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Actions'))->view('components.buttons.coupon_actions'),
        ];
    }
}
