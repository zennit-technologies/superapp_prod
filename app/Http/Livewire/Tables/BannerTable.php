<?php

namespace App\Http\Livewire\Tables;

use App\Models\Banner;
use Kdion4891\LaravelLivewireTables\Column;

class BannerTable extends BaseTableComponent
{

    public $model = Banner::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return Banner::with('category','vendor');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Link'),'link')->searchable(),
            Column::make(__('Vendor'),'vendor.name')->searchable(),
            Column::make(__('Category'),'category.name')->searchable(),
            Column::make(__('Image'))->view('components.table.image_md'),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
