<?php

namespace App\Http\Livewire\Tables;


use App\Models\SmsGateway;
use Kdion4891\LaravelLivewireTables\Column;

class SmsGatewayTable extends BaseTableComponent
{

    public $model = SmsGateway::class;

    public function query()
    {
        return SmsGateway::query();
    }

    public function columns()
    {
        return [
            Column::make(__('ID'),"id")->searchable()->sortable(),
            Column::make(__('Name'),'name')->searchable()->sortable(),
            Column::make(__('Actions'))->view('components.buttons.simple_actions'),
            Column::make(__('Test'))->view('components.buttons.show'),
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
