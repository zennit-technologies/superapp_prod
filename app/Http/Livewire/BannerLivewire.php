<?php

namespace App\Http\Livewire;

use App\Models\Banner;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class BannerLivewire extends BaseLivewireComponent
{

    //
    public $model = Banner::class;

    //
    public $link;
    public $vendor_id;
    public $category_id;
    public $isActive;
    public $vendorSearchClause = [];


    public function render()
    {
        return view('livewire.banners', [
            "categories" => Category::active()->get(),
        ]);
    }

    public function showCreateModal()
    {
        $this->showCreate = true;
        $this->category_id = Category::active()->first()->id ?? null;
    }

    //select actions
    public function autocompleteVendorSelected($vendor)
    {

        try {
            $this->vendor_id = $vendor['id'];
        } catch (\Exception $ex) {
            logger("Error", [$ex]);
        }
    }


    public function save()
    {
        //validate
        $this->validate(
            [
                "category_id" => "required|exists:categories,id",
                "link" => "sometimes|nullable|url",
                "photo" => "required|image|max:4096",
            ],
            [
                "photo.max" => __("Photo must be less than 4MB")
            ]
        );

        try {

            DB::beginTransaction();
            $model = new Banner();
            $model->category_id = $this->category_id;
            $model->vendor_id = $this->vendor_id ?? null;
            $model->link = $this->link ?? '';
            $model->is_active = $this->isActive ?? false;
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection();
                $model->addMedia($this->photo->getRealPath())->toMediaCollection();
                $this->photo = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Banner created successfully!"));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Banner creation failed!"));
        }
    }

    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->category_id = $this->selectedModel->category_id;
        $this->vendor_id = $this->selectedModel->vendor_id;
        $this->link = $this->selectedModel->link;
        $this->isActive = $this->selectedModel->is_active;
        $this->emit('preselectedVendorEmit', $this->selectedModel->vendor->name ?? "");
        $this->emit('showEditModal');
    }

    public function update()
    {
        //validate
        $this->validate(
            [
                "category_id" => "required|exists:categories,id",
                "link" => "sometimes|nullable|url",
                "photo" => "sometimes|nullable|image|max:4096",
            ]
        );

        try {

            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->category_id = $this->category_id;
            $model->vendor_id = $this->vendor_id ?? '';
            $model->link = $this->link ?? '';
            $model->is_active = $this->isActive ?? false;
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection();
                $model->addMedia($this->photo->getRealPath())->toMediaCollection();
                $this->photo = null;
            }

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Banner updated successfully!"));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Banner updated failed!"));
        }
    }
}
