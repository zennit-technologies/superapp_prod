<?php

namespace App\Http\Livewire\Tables\Taxi;

use App\Http\Livewire\Tables\BaseTableComponent;
use App\Models\VehicleType;
use Kdion4891\LaravelLivewireTables\Column;

class VehicleTypeTable extends BaseTableComponent
{

    public $model = VehicleType::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return VehicleType::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Image'))->view('components.table.image_sm'),
            Column::make(__('Name'),'name')->searchable(),
            Column::make(__('Base Fare'),'base_fare')->searchable(),
            Column::make(__('Distance Fare')."/km",'distance_fare')->searchable(),
            Column::make(__('Fare Per Minutes'),'time_fare')->searchable(),
            Column::make(__('Minimum Fare'),'min_fare')->searchable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Actions'))->view('components.buttons.no_delete_actions'),
        ];
    }
}
