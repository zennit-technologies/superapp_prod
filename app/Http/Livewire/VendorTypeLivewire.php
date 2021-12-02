<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Day;
use App\Models\VendorType;
use Exception;
use Illuminate\Support\Facades\DB;

class VendorTypeLivewire extends BaseLivewireComponent
{

    //
    public $model = VendorType::class;

    //
    public $name;
    public $color;
    public $description;
    public $isActive;
    public $slug;
    public $types = [];


    protected $messages = [
        "photo.max" => "Logo must be not be more than 1MB",
    ];

    public function render()
    {
        
        $this->types = VendorType::distinct()->whereNotIn('slug',['parcel','package','taxi'])->get(['slug'])->pluck('slug');
        return view('livewire.vendor_types');
    }


    // Updating model
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->color = $this->selectedModel->color;
        $this->description = $this->selectedModel->description;
        $this->isActive = $this->selectedModel->is_active;
        $this->emit('showEditModal');
    }

    public function save()
    {
        //validate
        $this->validate(
            [
                "name" => "required|string",
                "description" => "required|string",
                "photo" => "nullable|sometimes|image|max:1024",
            ]
        );

        try {
            $this->isDemo();
            DB::beginTransaction();
            $model = new VendorType();
            $model->name = $this->name;
            $model->color = $this->color;
            $model->description = $this->description;
            $model->is_active = $this->isActive;
            $model->slug = $this->slug ?? "food";
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection("logo");
                $model->addMedia($this->photo->getRealPath())->toMediaCollection("logo");
                $this->photo = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Vendor Type") . " " . __('created successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor Type") . " " . __('creation failed!'));
        }
    }

    public function update()
    {
        //validate
        $this->validate(
            [
                "name" => "required|string",
                "description" => "required|string",
                "photo" => "nullable|sometimes|image|max:1024",
            ]
        );

        try {
            $this->isDemo();
            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->name = $this->name;
            $model->color = $this->color;
            $model->description = $this->description;
            $model->is_active = $this->isActive;
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection("logo");
                $model->addMedia($this->photo->getRealPath())->toMediaCollection("logo");
                $this->photo = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Vendor Type") . " " . __('updated successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor Type") . " " . __('updated failed!'));
        }
    }

    
}
