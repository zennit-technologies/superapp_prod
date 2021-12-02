<?php

namespace App\Http\Livewire\Tables;

use App\Models\OptionGroup;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class OptionsGroupTable extends BaseTableComponent
{

    public $model = OptionGroup::class;
    public $header_view = 'components.buttons.new';

    public function mount()
    {
        $this->showHeader();

        //
        $this->setTableProperties();
    }

    public function showHeader(){
        if (!Auth::user()->hasRole('manager')) {
            $this->header_view = null;
            $this->canManage = false;
        }else{
            $this->header_view = 'components.buttons.new';
            $this->canManage = true;
        }
        $this->setTableProperties();
    }

    public function query()
    {
        $this->showHeader();

        if (Auth::user()->hasRole('admin')) {
            return OptionGroup::query();
        }else{
            return OptionGroup::where('vendor_id', Auth::user()->vendor_id);
        }
    }

    public function columns()
    {

        $columns = [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Multiple'))->view('components.table.multiple'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.actions')
        ];

        //
        // if ($this->canManage) {
        //     array_push(
        //         $columns,
        //         Column::make('Actions')->view('components.buttons.actions')
        //     );
        // }
        return $columns;
    }
}
