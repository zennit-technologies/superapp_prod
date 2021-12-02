<?php

namespace App\Http\Livewire\Tables;

use App\Models\Country;
use Kdion4891\LaravelLivewireTables\Column;

class CountryTable extends BaseTableComponent
{

    public $model = Country::class;
    public $per_page = 40;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return Country::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id"),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Actions'))->view('components.buttons.edit'),
        ];
    }
}
