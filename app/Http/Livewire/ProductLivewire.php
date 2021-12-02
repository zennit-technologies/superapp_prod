<?php

namespace App\Http\Livewire;

use App\Models\Menu;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductLivewire extends BaseLivewireComponent
{

    //
    public $model = Product::class;

    //
    public $name;
    public $description;
    public $price;
    public $sku;
    public $discount_price = 0;
    public $capacity;
    public $unit;
    public $package_count;
    public $available_qty;
    public $vendorID;
    public $plus_option;
    public $deliverable = 1;
    public $isActive = 1;

    //
    public $menusIDs = [];
    public $categoriesIDs;
    public $subCategoriesIDs = [];
    public $photos = [];
    //
    public $showAssignSubcategories = false;
    public $subCategories = [];


    protected $rules = [
        "name" => "required|string",
        "price" => "required|numeric",
        "vendorID" => "required|exists:vendors,id",
    ];


    protected $messages = [
        "vendorID.exists" => "Invalid vendor selected",
    ];

    public function render()
    {

        return view('livewire.products', [
            "vendors" => [],
            "menus" => Menu::active()->where('vendor_id', $this->vendorID)->get(),
            "categories" => [],
            "subcategories" => [],
        ]);
    }


    public function showCreateModal()
    {
        $this->reset();
        $this->showCreate = true;
        $this->showSelect2("#vendorSelect2", $this->vendorID, "vendorChange", $this->getVendors());
        $this->vendorChange($this->vendorID);
    }

    public function save()
    {
        //validate
        $this->validate();

        try {

            DB::beginTransaction();
            $model = new Product();
            $model->name = $this->name;
            $model->sku = $this->sku ?? null;
            $model->description = $this->description;
            $model->price = $this->price;
            $model->discount_price = $this->discount_price;
            $model->capacity = $this->capacity;
            $model->unit = $this->unit;
            $model->package_count = $this->package_count;
            $model->available_qty = !empty($this->available_qty) ? $this->available_qty : null;
            $model->vendor_id = $this->vendorID;
            $model->featured = false;
            $model->plus_option = $this->plus_option ?? true;
            $model->deliverable = $this->deliverable;
            $model->is_active = $this->isActive;
            $model->save();

            if ($this->photos) {

                $model->clearMediaCollection();
                foreach ($this->photos as $photo) {
                    $model->addMedia($photo)->toMediaCollection();
                }
                $this->photos = null;
            }

            //
            $model->categories()->attach($this->categoriesIDs);

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Product") . " " . __('created successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Product") . " " . __('creation failed!'));
        }
    }

    // Updating model
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->sku = $this->selectedModel->sku;
        $this->description = $this->selectedModel->description;
        $this->price = $this->selectedModel->price;
        $this->discount_price = $this->selectedModel->discount_price;
        $this->capacity = $this->selectedModel->capacity;
        $this->unit = $this->selectedModel->unit;
        $this->package_count = $this->selectedModel->package_count;
        $this->available_qty = $this->selectedModel->available_qty;
        $this->vendorID = $this->selectedModel->vendor_id;
        $this->plus_option = $this->selectedModel->plus_option ?? true;
        $this->deliverable = $this->selectedModel->deliverable;
        $this->isActive = $this->selectedModel->is_active;


        $this->emit('showEditModal');
        $this->showSelect2("#editVendorSelect2", $this->vendorID, "vendorChange", $this->getVendors());

        $this->categoriesIDs = $this->selectedModel->categories()->pluck('category_id');
        $this->vendorID = $this->selectedModel->vendor_id;
        $categories = $this->getCategories();
        $this->showSelect2("#editCategoriesSelect2", $this->categoriesIDs, "categoriesChange", $categories);
        
    }

    public function update()
    {
        //validate
        $this->validate(
            [
                "name" => "required|string",
                "price" => "required|numeric",
                "vendorID" => "required|exists:vendors,id",
                "photo" => "nullable|sometimes|image|max:2048",
            ]
        );

        try {

            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->name = $this->name;
            $model->sku = $this->sku ?? null;
            $model->description = $this->description;
            $model->price = $this->price;
            $model->discount_price = $this->discount_price;
            $model->capacity = $this->capacity;
            $model->unit = $this->unit;
            $model->package_count = $this->package_count;
            $model->available_qty = !empty($this->available_qty) ? $this->available_qty : null;
            $model->vendor_id = $this->vendorID;
            $model->plus_option = $this->plus_option ?? true;
            $model->deliverable = $this->deliverable;
            $model->is_active = $this->isActive;
            $model->save();

            if ($this->photos) {

                $model->clearMediaCollection();
                foreach ($this->photos as $photo) {
                    $model->addMedia($photo)->toMediaCollection();
                }
                $this->photos = null;
            }

            //
            $model->categories()->sync($this->categoriesIDs);

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Product") . " " . __('updated successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Product") . " " . __('updated failed!'));
        }
    }

    //

    // Updating model
    public function initiateAssign($id)
    {

        $this->selectedModel = $this->model::find($id);
        $this->menusIDs = $this->selectedModel->menus()->pluck('id')->toArray();
        $this->menusIDs = array_map(
            function ($value) {
                return (string)$value;
            },
            $this->menusIDs
        );
        $this->vendorID = $this->selectedModel->vendor_id;
        $this->emit('showAssignModal');
    }

    public function assignMenus()
    {

        //
        $this->selectedModel->menus()->sync($this->menusIDs);
        $this->dismissModal();
        $this->reset();
        $this->showSuccessAlert(__("Product") . " " . __('updated successfully!'));
        $this->emit('refreshTable');
    }

    // Updating subcategories
    public function initiateSubcategoriesAssign($id)
    {

        $this->selectedModel = $this->model::find($id);
        $this->subCategoriesIDs = $this->selectedModel->sub_categories()->pluck('id')->toArray();
        $this->subCategoriesIDs = array_map(
            function ($value) {
                return (string)$value;
            },
            $this->subCategoriesIDs
        );
        //
        $productCategoriesID = $this->selectedModel->categories()->pluck('id')->toArray();
        $this->subCategories = Subcategory::whereIn('category_id', $productCategoriesID)->get();
        $this->showAssignSubcategories = true;
    }


    public function assignSubcategories()
    {

        //
        $this->selectedModel->sub_categories()->sync($this->subCategoriesIDs);
        $this->dismissModal();
        $this->reset();
        $this->showSuccessAlert(__("Product") . " " . __('updated successfully!'));
        $this->emit('refreshTable');
    }






    // 
    public function vendorChange($data)
    {
        $this->vendorID = $data;

        $categories = $this->getCategories();
        if ($this->showCreate) {
            $this->showSelect2("#categoriesSelect2", $this->categoriesIDs, "categoriesChange", $categories);
        } else {
            $this->showSelect2("#editCategoriesSelect2", $this->categoriesIDs, "categoriesChange", $categories);
        }
    }

    public function categoriesChange($data)
    {
        $this->categoriesIDs = $data;
    }


    //
    public function photoSelected($photos)
    {
        $this->photos = $photos;
    }


    public function getVendors()
    {
        $vendors = [];
        if (User::find(Auth::id())->hasRole('admin')) {
            $this->vendorID = Vendor::active()->first()->id ?? null;
            $vendors = Vendor::active()->get();
        } else {
            $this->vendorID = Auth::user()->vendor_id;
            $vendors = Vendor::where('id', $this->vendorID)->get();
        }
        return $vendors;
    }

    public function getCategories()
    {
        $selectedVendor = Vendor::find($this->vendorID);
        return Category::where('vendor_type_id', $selectedVendor->vendor_type_id ?? "")->get();
    }
}
