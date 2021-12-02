<?php

namespace App\Http\Livewire;

use App\Models\State;
use App\Models\Country;
use Exception;
use Illuminate\Support\Facades\DB;
class StateLivewire extends BaseLivewireComponent
{


     //
     public $model = State::class;

     //
     public $name;
     public $country_id;
 
     protected $rules = [
         "name" => "required|string",
         "country_id" => "required|exists:countries,id",
     ];

     
    public function render()
    {

        $countries = Country::get();
        return view('livewire.states',[
            "countries" => $countries,
        ]);
    }


    public function save(){
        //validate
        $this->validate();

        try{

            DB::beginTransaction();
            $model = new State();
            $model->name = $this->name;
            $model->country_id = $this->country_id;
            $model->save();

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("State")." ".__('created successfully!'));
            $this->emit('refreshTable');


        }catch(Exception $error){
            DB::rollback();
            $this->showErrorAlert( $error->getMessage() ?? __("State")." ".__('creation failed!'));

        }

    }

    public function initiateEdit($id){
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->country_id = $this->selectedModel->country->id;
        $this->emit('showEditModal');
    }

    public function update(){
        //validate
        $this->validate();

        try{

            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->name = $this->name;
            $model->country_id = $this->country_id;
            $model->save();

            DB::commit();

            $this->dismissModal();
            $this->reset();
                        $this->showSuccessAlert(__("State")." ".__('updated successfully!'));
            $this->emit('refreshTable');


        }catch(Exception $error){
            DB::rollback();
                        $this->showErrorAlert( $error->getMessage() ?? __("State")." ".__('updated failed!'));

        }

    }



}
