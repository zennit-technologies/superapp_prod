<?php

namespace App\Http\Livewire\Tables;

use App\Models\VendorType;
use Kdion4891\LaravelLivewireTables\Column;
use Illuminate\Support\Facades\Auth;

class VendorTypeTable extends BaseTableComponent
{

    public $model = VendorType::class;
    public $checkDemo = true;
    public $header_view = 'components.buttons.new';
   

    public function query()
    {
        return VendorType::query();
    }

    public function columns()
    {

        $this->mount();
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Logo'))->view('components.table.logo'),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make('Color','color'),
            Column::make(__('Description'),'description')->searchable(),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.simple_actions'),
        ];
    }

    //
    public function deleteModel(){

        try{
            $this->showErrorAlert( "Delete Operation Not Allowed");
            return;
            $this->isDemo();
            \DB::beginTransaction();
            $this->selectedModel->delete();
            \DB::commit();
            $this->showSuccessAlert("Deleted");
        }catch(Exception $error){
            \DB::rollback();
            $this->showErrorAlert( $error->getMessage() ?? "Failed");
        }
    }

}
