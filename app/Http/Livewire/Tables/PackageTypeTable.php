<?php

namespace App\Http\Livewire\Tables;


use App\Models\PackageType;
use Kdion4891\LaravelLivewireTables\Column;

class PackageTypeTable extends BaseTableComponent
{

    public $model = PackageType::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return PackageType::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable(),
            Column::make(__('Description')),
            Column::make(__('Image'))->view('components.table.image_md'),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
