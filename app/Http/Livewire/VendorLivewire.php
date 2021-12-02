<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Day;
use App\Models\DeliveryZone;
use App\Models\VendorType;
use Exception;
use Illuminate\Support\Facades\DB;

class VendorLivewire extends BaseLivewireComponent
{

    //
    public $model = Vendor::class;
    public $showDayAssignment = false;
    public $showNewDayAssignment = false;

    //
    public $name;
    public $description;
    public $base_delivery_fee;
    public $delivery_fee;
    public $charge_per_km;
    public $delivery_range;
    public $min_order;
    public $max_order;
    public $phone;
    public $email;
    public $address;
    public $latitude;
    public $longitude;
    public $commission;
    public $tax;
    public $pickup;
    public $delivery;
    public $isActive;
    public $auto_assignment;
    public $auto_accept;
    public $allow_schedule_order;
    public $has_sub_categories;
    public $use_subscription = false;
    public $vendor_type_id;
    public $vendorTypes;
    public $isPackageVendor = false;
    public $isServiceVendor = false;
    public $deliveryZones;
    public $deliveryZoneID;

    //
    public $managersIDs;
    public $categoriesIDs;
    public $days;
    public $workingDays;
    public $workingDaysExcluded = [];
    public $dayOpen = [];
    public $dayClose = [];
    //new day
    public $newSelectedDay;
    public $newDayOpen;
    public $newDayClose;


    protected $rules = [
        "name" => "required|string",
        "description" => "required|string",
        "base_delivery_fee" => 'nullable|sometimes|numeric|required_if:is_package_vendor,0,false',
        "delivery_fee" => 'nullable|sometimes|numeric|required_if:is_package_vendor,0,false',
        "delivery_range" => 'nullable|sometimes|numeric|required_if:is_package_vendor,0,false',
        "vendor_type_id" => 'required|exists:vendor_types,id',
        "phone" => "required|numeric",
        "email" => "required|email|unique:vendors,email",
        "address" => "required|string",
        "latitude" => "required|numeric",
        "longitude" => "required|numeric",
        "commission" => "nullable|sometimes|numeric",
        "tax" => "nullable|sometimes|numeric",
        "photo" => "required|image|max:1024",
        "secondPhoto" => "required|image|max:2048",
    ];


    protected $messages = [
        "photo.max" => "Logo must be not be more than 1MB",
        "photo.required" => "Logo is required",
        "secondPhoto.max" => "Feature Image must be not be more than 2MB",
        "secondPhoto.required" => "Feature Image is required",
        "email.unique" => "Email already used by another vendor",
    ];

    public function render()
    {
        $this->vendorTypes = VendorType::active()->assignable()->get();
        $this->deliveryZones = DeliveryZone::active()->get();
        return view('livewire.vendors');
    }


    public function updatedVendorTypeId($value)
    {
        //
        $vendorType = VendorType::find($value);
        $this->isPackageVendor = $vendorType->slug == "parcel";
        $this->isServiceVendor = $vendorType->slug == "service";
        $this->updateCategorySelector();
    }

    public function showCreateModal()
    {
        $this->reset();
        $this->showCreate = true;
        $this->vendorTypes = VendorType::active()->get();
        $this->vendor_type_id = $this->vendorTypes->first()->id;
        $this->updatedVendorTypeId($this->vendor_type_id);
        $this->emit('initialAddressSelected', '');
    }

    public function save()
    {
        //validate
        $this->validate();

        try {

            DB::beginTransaction();
            $model = new Vendor();
            $model->name = $this->name;
            $model->description = $this->description;
            $model->base_delivery_fee = $this->base_delivery_fee ?? 0;
            $model->delivery_fee = $this->delivery_fee ?? 0;
            $model->charge_per_km = $this->charge_per_km ?? 0;
            $model->delivery_range = $this->delivery_range ?? 0;
            $model->phone = $this->phone;
            $model->email = $this->email;
            $model->address = $this->address;
            $model->latitude = $this->latitude;
            $model->longitude = $this->longitude;
            $model->commission = $this->commission ?? 0;
            $model->tax = $this->tax ?? 0;
            $model->pickup = $this->pickup ?? 0;
            $model->delivery = $this->delivery ?? 0;
            $model->min_order = $this->min_order;
            $model->max_order = $this->max_order;
            $model->is_active = $this->isActive ?? false;
            $model->auto_assignment = $this->auto_assignment ?? false;
            $model->auto_accept = $this->auto_accept ?? false;
            $model->allow_schedule_order = $this->allow_schedule_order ?? false;
            $model->has_sub_categories = $this->has_sub_categories ?? false;
            $model->vendor_type_id = $this->vendor_type_id;
            $model->use_subscription = $this->use_subscription ?? false;
            $model->delivery_zone_id = $this->deliveryZoneID;
            //creator
            $model->creator_id = \Auth::id();
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection();
                $model->addMedia($this->photo->getRealPath())->toMediaCollection("logo");
                $this->photo = null;
            }

            if ($this->secondPhoto) {

                $model->clearMediaCollection();
                $model->addMedia($this->secondPhoto->getRealPath())->toMediaCollection("feature_image");
                $this->secondPhoto = null;
            }

            //
            $model->categories()->attach($this->categoriesIDs);

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Vendor") . " " . __('created successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor") . " " . __('creation failed!'));
        }
    }

    // Updating model
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->description = $this->selectedModel->description;
        $this->base_delivery_fee = $this->selectedModel->base_delivery_fee;
        $this->delivery_fee = $this->selectedModel->delivery_fee;
        $this->delivery_range = $this->selectedModel->delivery_range;
        $this->phone = $this->selectedModel->phone;
        $this->email = $this->selectedModel->email;
        $this->address = $this->selectedModel->address;
        $this->latitude = $this->selectedModel->latitude;
        $this->longitude = $this->selectedModel->longitude;
        $this->commission = $this->selectedModel->commission;
        $this->tax = $this->selectedModel->tax;
        $this->pickup = $this->selectedModel->pickup;
        $this->delivery = $this->selectedModel->delivery;
        $this->min_order = $this->selectedModel->min_order;
        $this->max_order = $this->selectedModel->max_order;
        $this->isActive = $this->selectedModel->is_active;
        $this->vendor_type_id = $this->selectedModel->vendor_type_id;
        $this->auto_assignment = $this->selectedModel->auto_assignment;
        $this->auto_accept = $this->selectedModel->auto_accept;
        $this->allow_schedule_order = $this->selectedModel->allow_schedule_order;
        $this->has_sub_categories = $this->selectedModel->has_sub_categories;
        $this->charge_per_km = $this->selectedModel->charge_per_km;
        $this->use_subscription = $this->selectedModel->use_subscription ?? false;
        $this->deliveryZoneID = $this->selectedModel->delivery_zone_id;

        
        //
        $this->updatedVendorTypeId($this->selectedModel->vendor_type_id);

        $this->categoriesIDs = $this->selectedModel->categories()->pluck('category_id');
        $this->updateCategorySelector();
        $this->emit('showEditModal');
        $this->emit('initialAddressSelected', $this->address);
    }

    public function update()
    {
        //validate
        $this->validate(
            [
                "name" => "required|string",
                "description" => "required|string",
                "delivery_fee" => "nullable|sometimes|numeric",
                "delivery_range" => "nullable|sometimes|numeric",
                "phone" => "required|numeric",
                "email" => "required|email|unique:vendors,email," . $this->selectedModel->id . "",
                "address" => "required|string",
                "latitude" => "required|numeric",
                "longitude" => "required|numeric",
                "commission" => "required|numeric",
                "tax" => "required|numeric",
                "photo" => "nullable|sometimes|image|max:1024",
                "secondPhoto" => "nullable|sometimes|image|max:2048",
            ]
        );

        try {

            DB::beginTransaction();
            $model = $this->selectedModel;
            $model->name = $this->name;
            $model->description = $this->description;
            $model->base_delivery_fee = $this->base_delivery_fee ?? 0;
            $model->delivery_fee = $this->delivery_fee;
            $model->charge_per_km = $this->charge_per_km;
            $model->delivery_range = $this->delivery_range;
            $model->phone = $this->phone;
            $model->email = $this->email;
            $model->address = $this->address;
            $model->latitude = $this->latitude;
            $model->longitude = $this->longitude;
            $model->commission = $this->commission;
            $model->tax = $this->tax;
            $model->pickup = $this->pickup;
            $model->delivery = $this->delivery;
            $model->min_order = $this->min_order;
            $model->max_order = $this->max_order;
            $model->is_active = $this->isActive;
            $model->vendor_type_id = $this->vendor_type_id;
            $model->auto_assignment = $this->auto_assignment;
            $model->auto_accept = $this->auto_accept;
            $model->allow_schedule_order = $this->allow_schedule_order;
            $model->has_sub_categories = $this->has_sub_categories;
            $model->use_subscription = $this->use_subscription;
            $model->delivery_zone_id = $this->deliveryZoneID;
            $model->save();

            if ($this->photo) {

                $model->clearMediaCollection("logo");
                $model->addMedia($this->photo->getRealPath())->toMediaCollection("logo");
                $this->photo = null;
            }

            if ($this->secondPhoto) {

                $model->clearMediaCollection("feature_image");
                $model->addMedia($this->secondPhoto->getRealPath())->toMediaCollection("feature_image");
                $this->secondPhoto = null;
            }

            //
            $model->categories()->sync($this->categoriesIDs);

            DB::commit();

            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Vendor") . " " . __('updated successfully!'));
            $this->emit('refreshTable');
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor") . " " . __('updated failed!'));
        }
    }

    // Assigning managers
    public function initiateAssign($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->managersIDs = $this->selectedModel->managers()->pluck('id');
        $managers =  User::manager()->get();
        $this->showSelect2("#managersSelect2", $this->managersIDs, "managersChange", $managers);
        $this->emit('showAssignModal');
    }


    public function managersChange($data)
    {
        $this->managersIDs = $data;
    }

    public function categoriesChange($data)
    {
        $this->categoriesIDs = $data;
    }

    public function updateCategorySelector()
    {
        $cateogires = Category::active()->where('vendor_type_id', $this->vendor_type_id)->get();
        if ($this->showCreate) {
            $this->showSelect2("#categoriesSelect2", $this->categoriesIDs, "categoriesChange", $cateogires);
        } else {
            $this->showSelect2("#editCategoriesSelect2", $this->categoriesIDs, "categoriesChange", $cateogires);
        }
    }



    //
    public function assignManagers()
    {

        try {

            DB::beginTransaction();

            //remove all managers
            User::where('vendor_id', $this->selectedModel->id)
                ->update(['vendor_id' => null]);

            //assigning
            foreach ($this->managersIDs as $managerId) {
                $manager = User::findorfail($managerId);
                $manager->vendor_id = $this->selectedModel->id;
                $manager->save();
            }

            DB::commit();
            $this->emit('dismissModal');
            $this->showSuccessAlert(__("Vendor Managers") . " " . __('created successfully!'));
        } catch (Exception $error) {
            DB::rollback();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor Managers") . " " . __('creation failed!'));
        }
    }

    //
    public function autocompleteAddressSelected($data)
    {
        $this->address = $data["address"];
        $this->latitude = $data["latitude"];
        $this->longitude = $data["longitude"];
    }



    //CUSTOM DAYS
    public function changeVendorTiming($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->days = Day::get();
        $vendorDays = $this->selectedModel->days;
        foreach ($vendorDays as $vendorDay) {
            $this->workingDays[] = $vendorDay;
            $this->dayOpen[$vendorDay->pivot->id] = $vendorDay->pivot->open;
            $this->dayClose[$vendorDay->pivot->id] = $vendorDay->pivot->close;
        }

        $this->showDayAssignment = true;
    }

    public function removeDay($id)
    {
        $this->selectedModel->refresh();
        $vendorDays = $this->selectedModel->days;
        $this->workingDays = [];
        foreach ($vendorDays as $vendorDay) {
            if ($vendorDay->pivot->id != $id) {
                $this->workingDays[] = $vendorDay;
                $this->dayOpen[$vendorDay->id] = $vendorDay->pivot->open;
                $this->dayClose[$vendorDay->id] = $vendorDay->pivot->close;
            } else {
                $vendorDay->pivot->delete();
            }
        }
    }



    public function saveDays()
    {
        //
        try {

            $dayVendor = [];
            foreach ($this->workingDays as $workingDay) {

                $pivotId = $workingDay["pivot"]["id"] ?? '';

                //
                $openTime = $this->dayOpen[$pivotId] ?? null;
                $closeTime = $this->dayClose[$pivotId] ?? null;

                if ($openTime == null || $closeTime == null) {
                    $this->resetValidation();
                    $this->addError('dayOpen.' . $pivotId . '', __('Both time must be supplied'));
                    $this->addError('dayClose.' . $pivotId . '', __('Both time must be supplied'));
                    return;
                }

                //
                if ($openTime != null && $closeTime != null) {
                    array_push($dayVendor, [
                        "day_id" => $workingDay["id"],
                        "vendor_id" => $this->selectedModel->id,
                        "open" => $openTime,
                        "close" => $closeTime,
                    ]);
                }
            }

            //
            $this->selectedModel->days()->detach();
            $this->selectedModel->days()->sync($dayVendor);
            $this->resetValidation();
            $this->emit('dismissModal');
            $this->showSuccessAlert(__("Vendor Open/close time") . " " . __("updated successfully!"));
        } catch (Exception $error) {

            DB::rollback();
            $this->resetValidation();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor Open/close time") . " " . __("update failed!"));
        }
    }

    public function saveNewDay()
    {
        //
        try {

            $dayVendor = [];
            $openTime = $this->newDayOpen ?? null;
            $closeTime = $this->newDayClose ?? null;

            if (($openTime != null && $closeTime == null) || ($openTime == null && $closeTime != null)) {
                $this->resetValidation();
                $this->addError('newDayOpen', __('Both time must be supplied'));
                $this->addError('newDayClose', __('Both time must be supplied'));
                return;
            }

            //
            if ($openTime != null && $closeTime != null) {
                array_push($dayVendor, [
                    "day_id" => $this->newSelectedDay ?? $this->days->first()->id,
                    "vendor_id" => $this->selectedModel->id,
                    "open" => $openTime,
                    "close" => $closeTime,
                ]);
            }


            //
            // $this->selectedModel->days()->detach();
            $this->selectedModel->days()->attach($dayVendor);
            $this->resetValidation();
            $this->emit('dismissModal');
            $this->showSuccessAlert(__("Vendor Open/close time") . " " . __("updated successfully!"));
        } catch (Exception $error) {

            DB::rollback();
            $this->resetValidation();
            $this->showErrorAlert($error->getMessage() ?? __("Vendor Open/close time") . " " . __("update failed!"));
        }
    }
}
