<?php

namespace App\Http\Livewire\Tables\Taxi;

use App\Http\Livewire\Tables\BaseTableComponent;
use App\Models\Vehicle;
use Kdion4891\LaravelLivewireTables\Column;

class VehicleTable extends BaseTableComponent
{

    public $model = Vehicle::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return Vehicle::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Driver'),'driver.name')->searchable(),
            Column::make(__('Registration Number'),'reg_no')->searchable(),
            Column::make(__('Color'),'color'),
            Column::make(__('Car Make'),'car_model.car_make.name')->searchable(),
            Column::make(__('Car Model'),'car_model.name')->searchable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Actions'))->view('components.buttons.primary_actions'),
        ];
    }
}
