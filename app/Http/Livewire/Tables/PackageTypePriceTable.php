<?php

namespace App\Http\Livewire\Tables;


use App\Models\PackageTypePricing;
use Illuminate\Support\Facades\Auth;
use Kdion4891\LaravelLivewireTables\Column;

class PackageTypePriceTable extends BaseTableComponent
{

    public $model = PackageTypePricing::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return PackageTypePricing::with('package_type')->where('vendor_id', Auth::user()->vendor_id);
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->sortable(),
            Column::make(__('Package Type'),'package_type.name')->searchable(),
            Column::make(__('Max Booking Days'), 'max_booking_days')->sortable(),
            Column::make(__('Base Price'),'base_price')->view('components.table.price')->sortable(),
            Column::make(__('Actions'))->view('components.buttons.crud_actions'),
        ];
    }
}
