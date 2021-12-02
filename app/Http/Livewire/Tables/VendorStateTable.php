<?php

namespace App\Http\Livewire\Tables;

use App\Models\StateVendor;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class VendorStateTable extends BaseTableComponent
{

    public $model = StateVendor::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return StateVendor::with('state.country','vendor')->where('vendor_id', Auth::user()->vendor_id );
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->sortable(),
            Column::make(__('Name'),'state.name')->searchable()->sortable(),
            Column::make(__('Country'),"state.country.name")->searchable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
