<?php

namespace App\Http\Livewire\Tables\Reports;

use App\Exports\CouponsExport;
use App\Http\Livewire\Tables\BaseDataTableComponent;
use App\Models\CouponUser;
use Maatwebsite\Excel\Facades\Excel;

use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class CouponReportTable extends BaseDataTableComponent
{

    public $model = CouponUser::class;
    public array $bulkActions = [
        'exportSelected' => 'Export',
    ];

    public function query()
    {
        return CouponUser::with(['user', 'coupon', 'order' => function ($query) {

            return $query->when($this->getFilter('start_date'), fn ($query, $sDate) => $query->whereDate('created_at', ">=", $sDate))
                ->when($this->getFilter('end_date'), fn ($query, $eDate) => $query->whereDate('created_at', "<=", $eDate));
        }]);
    }

    public function columns(): array
    {
        return [
            Column::make(__('ID'), 'id'),
            Column::make(__('Code'), 'coupon.code')->searchable()->sortable(),
            Column::make(__('Discount') . "(" . setting('currency', '$') . ")", 'order.discount')->searchable()->sortable(),
            Column::make(__('User'), 'user.name'),
            Column::make(__('Order'), 'order.code'),
            Column::make(__('Date'), 'order.created_at'),
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
            return Excel::download(new CouponsExport($this->selectedKeys), 'coupons.xlsx');
        } else {
            //
        }
    }
}
