<?php

namespace App\Http\Livewire\Tables;


use App\Models\PaymentMethod;
use Kdion4891\LaravelLivewireTables\Column;

class PaymentMethodTable extends BaseTableComponent
{

    public $model = PaymentMethod::class;

    public function query()
    {
        return PaymentMethod::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Image'))->view('components.table.image_sm'),
            Column::make(__('Active'))->view('components.table.active'),
            Column::make(__('Created At'), 'formatted_date'),
            Column::make(__('Actions'))->view('components.buttons.no_delete_actions'),
        ];
    }


    public function activateModel(){

        try{
            $this->isDemo();
            $this->selectedModel->is_active = true;
            $this->selectedModel->save();
            $this->showSuccessAlert(__("Activated"));
        }catch(\Exception $error){
            $this->showErrorAlert("Failed");
        }
    }


    public function deactivateModel(){

        try{
            $this->isDemo();
            $this->selectedModel->is_active = false;
            $this->selectedModel->save();
            $this->showSuccessAlert(__("Deactivated"));
        }catch(\Exception $error){
            $this->showErrorAlert("Failed");
        }
    }
}
