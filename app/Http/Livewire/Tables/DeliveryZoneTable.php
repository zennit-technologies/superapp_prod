<?php

namespace App\Http\Livewire\Tables;

use App\Models\DeliveryZone;
use App\Models\Review;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class DeliveryZoneTable extends BaseDataTableComponent
{

    public $model = DeliveryZone::class;
    public $header_view = 'components.buttons.new';

    public function filters(): array
    {
        return [
            'start_date' => Filter::make(__('Start Date'))
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ]),
            'end_date' => Filter::make(__('End Date'))
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ])
        ];
    }

    public function query()
    {
        return $this->model::withCount('vendors')
            ->when($this->getFilter('start_date'), fn ($query, $sDate) => $query->whereDate('created_at', ">=", $sDate))
            ->when($this->getFilter('end_date'), fn ($query, $eDate) => $query->whereDate('created_at', "<=", $eDate))
            ->orderBy('created_at', 'DESC');
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), "id")->searchable()->sortable(),
            Column::make(__('Name'), 'name')->searchable()->sortable(),
            Column::make(__('Vendors'), 'vendors_count'),
            Column::make(__('Active'))->format(function ($value, $column, $row) {
                return view('components.table.active', $data = [
                    "model" => $row
                ]);
            }),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->format(function ($value, $column, $row) {
                return view('components.buttons.simple_actions', $data = [
                    "model" => $row
                ]);
            }),
        ];
    }
}
