<?php

namespace App\Http\Livewire\Tables;

use App\Models\User;
use Kdion4891\LaravelLivewireTables\Column;

class DriverTable extends BaseTableComponent
{

    public $model = User::class;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return User::role('driver')->where('vendor_id', \Auth::user()->vendor_id);
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Email'),'email')->searchable()->sortable(),
            Column::make(__('Phone'),'phone')->searchable()->sortable(),
            Column::make(__('Wallet'),'wallet')->view('components.table.wallet'),
            Column::make(__('Commission')."(%)", 'commission'),
            Column::make(__('Role'), 'role_name'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions'),
        ];
    }
}
