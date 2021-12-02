<?php

namespace App\Http\Livewire\Tables;


use App\Models\Remittance;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class RemittanceTable extends BaseTableComponent
{

    public $model = Remittance::class;
    public $type;


    public function mount()
    {

        //
        $this->setTableProperties();
    }


    public function query()
    {
        $vendorId = \Auth::user()->vendor_id;
        return Remittance::with('user', 'order')->whereNotNull('user_id')->when($vendorId, function ($query) use ($vendorId) {
            return $query->whereHas("user", function ($q) use ($vendorId) {
                return $q->where('vendor_id', $vendorId);
            });
        })->when(\Auth::user()->hasAnyRole('city-admin'), function ($query) {
            return $query->whereHas("user", function ($query) {
                return $query->where('creator_id', Auth::id());
            });
        })->where('status',"pending");
    }

    public function columns()
    {

        $columns = [
            Column::make(__('ID'),"id"),
            Column::make(__('Amount'), 'order_total')->view('components.table.price')->sortable(),
            Column::make(__('Driver'), 'user.name')->searchable(),
            Column::make(__('Updated At'), 'formatted_updated_date'),
            Column::make(__('Actions'))->view('components.buttons.remittance_actions'),
        ];



        return $columns;
    }
}
