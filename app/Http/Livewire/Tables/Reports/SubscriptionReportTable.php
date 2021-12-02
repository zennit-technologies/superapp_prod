<?php

namespace App\Http\Livewire\Tables\Reports;

use App\Exports\SubscriptionReportExport;
use App\Http\Livewire\Tables\BaseDataTableComponent;
use App\Models\Subscription;
use Maatwebsite\Excel\Facades\Excel;

use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class SubscriptionReportTable extends BaseDataTableComponent
{

    public $model = Subscription::class;
    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];

    public array $filters = [];

    public function mount()
    {
        $this->filters = [
            'start_date' => now()->subDays(7)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ];
    }

    public function query()
    {
        //
        return $this->model::withCount(['vendors' => function ($query) {
            return $query->when($this->getFilter('start_date'), function ($query, $sDate) {
                return $query->whereDate('expires_at', ">=", $sDate);
            })->when($this->getFilter('end_date'), function ($query, $eDate) {
                return $query->whereDate('expires_at', "<=", $eDate);
            });
        }, 'successful_sub' => function ($query) {
            return $query->when($this->getFilter('start_date'), function ($query, $sDate) {
                return $query->whereDate('expires_at', ">=", $sDate);
            })->when($this->getFilter('end_date'), function ($query, $eDate) {
                return $query->whereDate('expires_at', "<=", $eDate);
            });
        }, 'pending_sub' => function ($query) {
            return $query->when($this->getFilter('start_date'), function ($query, $sDate) {
                return $query->whereDate('expires_at', ">=", $sDate);
            })->when($this->getFilter('end_date'), function ($query, $eDate) {
                return $query->whereDate('expires_at', "<=", $eDate);
            });
        }, 'failed_sub' => function ($query) {
            return $query->when($this->getFilter('start_date'), function ($query, $sDate) {
                return $query->whereDate('expires_at', ">=", $sDate);
            })->when($this->getFilter('end_date'), function ($query, $eDate) {
                return $query->whereDate('expires_at', "<=", $eDate);
            });
        }]);
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id'),
            Column::make(__('Name'), "name")->searchable()->sortable(),
            Column::make(__('Vendor Count'), 'vendors_count')->format(function ($value, $column, $row) {
                return view('components.table.count', $data = [
                    "model" => $row,
                    "value" => $value
                ]);
            })->sortable(),
            Column::make(__('Successful'), 'successful_sub_count')->format(function ($value, $column, $row) {
                return view('components.table.count', $data = [
                    "model" => $row,
                    "value" => $value
                ]);
            })->sortable(),
            Column::make(__('Pending'), 'pending_sub_count')->format(function ($value, $column, $row) {
                return view('components.table.count', $data = [
                    "model" => $row,
                    "value" => $value
                ]);
            })->sortable(),
            Column::make(__('Failed'), 'failed_sub_count')->format(function ($value, $column, $row) {
                return view('components.table.count', $data = [
                    "model" => $row,
                    "value" => $value
                ]);
            })->sortable(),
        ];
    }

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


    public function exportSelected()
    {
        $this->isDemo(true);
        if ($this->selectedRowsQuery->count() > 0) {
            $fileName = "subscription_report(";
            //
            if ($this->getFilter('start_date')) {
                $fileName .= $this->getFilter('start_date') . " - ";
            }
            //
            else if ($this->getFilter('end_date')) {
                $fileName .= $this->getFilter('end_date');
            }

            $fileName .= ").xlsx";
            return Excel::download(new SubscriptionReportExport($this->selectedRowsQuery), $fileName);
        } else {
            //
        }
    }
}
