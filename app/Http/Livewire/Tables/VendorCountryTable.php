<?php

namespace App\Http\Livewire\Tables;

use App\Models\CountryVendor;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class VendorCountryTable extends BaseTableComponent
{

    public $model = CountryVendor::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return CountryVendor::with('country','vendor')->where('vendor_id', Auth::user()->vendor_id );
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->sortable(),
            Column::make(__('Name'),'country.name')->searchable()->sortable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
