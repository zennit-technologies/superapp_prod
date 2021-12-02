<?php

namespace App\Http\Livewire;

use App\Models\Menu;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ServiceLivewire extends BaseLivewireComponent
{

    //
    public $model = Service::class;

    //
    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $vendor_id;
    public $per_hour;
    public $is_active;
    public $category_id;
    public $photos = [];

    //
    public $vendorSearchClause = ["id" => 0];
    public $categorySearchClause = ["id" => 0];


    protected $rules = [
        "name" => "required|string",
        "description" => "sometimes|nullable|string",
        "price" => "required|numeric",
        "discount_price" => "sometimes|nullable|numeric",
        "vendor_id" => "required|exists:vendors,id",
        "category_id" => "required|exists:categories,id",
        "per_hour" => "sometimes",
        "is_active" => "sometimes",
    ];


    protected $messages = [
        "vendor_id.exists" => "Invalid vendor selected",
        "category_id.exists" => "Invalid category selected",
    ];

    public function mount()
    {
        $user = User::find(Auth::id());
        if ($user->hasRole('admin')) {
            $this->vendorSearchClause = [];
        } else {
            $this->vendorSearchClause = ['id' => Auth::user()->vendor_id];
        }
    }

    public function render()
    {
        return view('livewire.services.service');
    }

    //
    public function autocompleteVendorSelected($vendor)
    {
        $this->vendor_id = $vendor["id"];
    }
    public function autocompleteCategorySelected($category)
    {
        $this->category_id = $category["id"];
    }


    public function save()
    {
        //validate
        $allData = $this->validate();

        try {

            DB::beginTransaction();
            $model = new Service($allData);
            $model->discount_price = $model->discount_price == null ? 0 : $model->discount_price;
            $model->save();

            if ($this->photos) {

                $model->clearMediaCollection();
                foreach ($this->photos as $photo) {
                    $model->addMedia($photo)->toMediaCollection();
                }
                $this->photos = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Service") . " " . __('created successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Service") . " " . __('creation failed!'));
        }
    }

    // Updating model
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->description = $this->selectedModel->description;
        $this->price = $this->selectedModel->price;
        $this->discount_price = $this->selectedModel->discount_price;
        $this->vendor_id = $this->selectedModel->vendor_id;
        $this->per_hour = $this->selectedModel->per_hour ?? false;
        $this->is_active = $this->selectedModel->is_active;
        $this->category_id = $this->selectedModel->category_id;

        $this->emit('preselectedVendorEmit', \Str::ucfirst($this->selectedModel->vendor->name ?? ''));
        $this->emit('preselectedCategoryEmit', \Str::ucfirst($this->selectedModel->category->name ?? ''));
        $this->emit('showEditModal');
    }

    public function update()
    {
        //validate
        $allData = $this->validate();

        try {

            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->fill($allData);
            $model->save();

            if ($this->photos) {

                $model->clearMediaCollection();
                foreach ($this->photos as $photo) {
                    $model->addMedia($photo)->toMediaCollection();
                }
                $this->photos = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Service") . " " . __('updated successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Service") . " " . __('updated failed!'));
        }
    }


    //
    public function photoSelected($photos)
    {
        $this->photos = $photos;
    }
}
