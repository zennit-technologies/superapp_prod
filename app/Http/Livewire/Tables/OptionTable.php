<?php

namespace App\Http\Livewire\Tables;

use App\Models\Option;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class OptionTable extends BaseTableComponent
{

    public $model = Option::class;
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
            return Option::with('option_group');
        }else{
            return Option::with('option_group')->where('vendor_id', Auth::user()->vendor_id);
        }
        
    }

    public function columns()
    {

        $columns = [
            Column::make(__('ID'),"id"),
            Column::make(__('Image'))->view('components.table.image_sm'),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Price'),'price')->view('components.table.price')->searchable()->sortable(),
            Column::make(__('Option Group'),'option_group.name')->searchable()->sortable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.option_actions')
        ];

        //
        // if( $this->canManage ){
        //     array_push($columns, Column::make('Actions')->view('components.buttons.actions'));
        // }
        return $columns;
    }
}
