<?php

namespace App\Http\Livewire\Tables;

use App\Models\State;
use Kdion4891\LaravelLivewireTables\Column;

class StateTable extends BaseTableComponent
{

    public $model = State::class;
    public $per_page = 20;
    public $header_view = 'components.buttons.new';

    public function query()
    {
        return State::with('country');
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Country'),"country.name")->searchable(),
            Column::make(__('Actions'))->view('components.buttons.edit'),
        ];
    }
}
