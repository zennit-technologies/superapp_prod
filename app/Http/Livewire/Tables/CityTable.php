<?php

namespace App\Http\Livewire\Tables;

use App\Models\City;
use Kdion4891\LaravelLivewireTables\Column;

class CityTable extends BaseTableComponent
{

    public $model = City::class;
    public $per_page = 100;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return City::with('state.country');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('State'),"state.name")->searchable(),
            Column::make(__('Country'),"state.country.name")->searchable(),
            Column::make(__('Actions'))->view('components.buttons.edit'),
        ];
    }
}
