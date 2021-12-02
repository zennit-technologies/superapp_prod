<?php

namespace App\Http\Livewire\Tables;

use App\Models\Earning;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class EarningTable extends BaseTableComponent
{

    public $model = Earning::class;
    public $type;


    public function mount()
    {

        //
        $this->setTableProperties();
    }


    public function query()
    {
        $vendorId = \Auth::user()->vendor_id;
        return Earning::with('user', 'vendor')->when($this->type == "vendors", function ($query) {
            return $query->whereNotNull('vendor_id')->when(\Auth::user()->hasAnyRole('city-admin'), function ($query) {
                return $query->whereHas("vendor", function ($query) {
                    return $query->where('creator_id', Auth::id());
                });
            });
        }, function ($query) use ($vendorId) {
            return $query->whereNotNull('user_id')->when($vendorId, function ($query) use ($vendorId) {
                return $query->whereHas("user", function ($q) use ($vendorId) {
                    return $q->where('vendor_id', $vendorId);
                });
            })->when(\Auth::user()->hasAnyRole('city-admin'), function ($query) {
                return $query->whereHas("user", function ($query) {
                    return $query->where('creator_id', Auth::id());
                });
            });
        })->where("amount",">", 0);
    }

    public function columns()
    {

        $columns = [
            Column::make(__('ID'),"id"),
            Column::make(__('Amount'),"amount")->view('components.table.price')->searchable()->sortable(),
        ];


        if ($this->type == "vendors") {
            array_push($columns, Column::make(__('Vendor'), 'vendor.name')->searchable());
        } else {
            array_push($columns, Column::make(__('Driver'), 'user.name')->searchable());
        }

        array_push($columns, Column::make(__('Updated At'), 'formatted_updated_date'));
        array_push($columns, Column::make(__('Actions'))->view('components.buttons.earning_actions'));
        return $columns;
    }
}
