<?php

namespace App\Http\Livewire\Tables;

use App\Models\Category;
use Kdion4891\LaravelLivewireTables\Column;

class CategoryTable extends BaseTableComponent
{

    public $model = Category::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return Category::with('vendor_type');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            // Column::make('Name','vendor_type.name'),
            Column::make(__('Image'))->view('components.table.image_md'),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
