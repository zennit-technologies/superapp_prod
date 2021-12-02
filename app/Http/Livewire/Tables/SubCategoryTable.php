<?php

namespace App\Http\Livewire\Tables;

use App\Models\Subcategory;
use Kdion4891\LaravelLivewireTables\Column;

class SubCategoryTable extends BaseTableComponent
{

    public $model = Subcategory::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return Subcategory::with('category');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Category'),'category.name'),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Image'))->view('components.table.image_md'),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
