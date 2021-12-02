<?php

namespace App\Http\Livewire;

use App\Models\DeliveryZone;
use App\Models\DeliveryZonePoint;
use Illuminate\Support\Facades\DB;
use Exception;

class DeliveryZoneLivewire extends BaseLivewireComponent
{

    public $model = DeliveryZone::class;
    public $name;
    public $is_active;
    public $coordinates;
    public $coordinateCollection;

    protected $rules = [
        "name" => "required",
        "coordinates" => "required",
    ];


    public function getListeners()
    {
        return $this->listeners + [
            'selectedCoordinates' => 'selectedCoordinates',
        ];
    }


    public function render()
    {
        return view('livewire.delivery_zones');
    }


    public function selectedCoordinates($coordinates)
    {
        $this->coordinateCollection = $coordinates;
        $this->coordinates = "";
        //
        foreach ($coordinates as $coordinate) {
            $this->coordinates .= "(" . $coordinate['lat'] . "," . $coordinate['lng'] . "), ";
        }
    }


    //
    function save()
    {

        $this->validate();

        try {
            DB::beginTransaction();
            $deliveryZone = new DeliveryZone();
            $deliveryZone->name = $this->name;
            $deliveryZone->is_active = $this->is_active ?? false;
            $deliveryZone->save();
            //points
            foreach ($this->coordinateCollection as $coordinate) {
                $deliveryZonePoint = new DeliveryZonePoint();
                $deliveryZonePoint->delivery_zone_id = $deliveryZone->id;
                $deliveryZonePoint->lat = $coordinate['lat'];
                $deliveryZonePoint->lng = $coordinate['lng'];
                $deliveryZonePoint->save();
            }

            DB::commit();
            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Delivery Zone") . " " . __('created successfully!'));
            $this->emit('resetMap');
            $this->emit('refreshTable');
        } catch (\Exception $ex) {
            DB::rollback();
            logger("Error", [$ex]);
            $this->showErrorAlert($ex->getMessage() ?? __("Delivery Zone") . " " . __('creation failed!'));
        }
    }
    
    
    //
    public function initiateEdit($id)
    {
        $this->selectedModel = $this->model::find($id);
        $this->name = $this->selectedModel->name;
        $this->is_active = $this->selectedModel->is_active;

        //
        $coordinates = [];
        foreach ($this->selectedModel->points as $point) {
            $coordinates[] = [
                "lat" => (float) $point->lat,
                "lng" => (float) $point->lng,
            ];
        }

        $this->selectedCoordinates($coordinates);
        $this->emit('initiateEditMap',$coordinates);
        $this->showEditModal();
    }


    function update()
    {

        $this->validate();

        try {
            DB::beginTransaction();
            $deliveryZone = $this->selectedModel;
            $deliveryZone->name = $this->name;
            $deliveryZone->is_active = $this->is_active ?? false;
            $deliveryZone->save();
            //
            $deliveryZone->points()->delete();
            // //points
            foreach ($this->coordinateCollection as $coordinate) {
                $deliveryZonePoint = new DeliveryZonePoint();
                $deliveryZonePoint->delivery_zone_id = $deliveryZone->id;
                $deliveryZonePoint->lat = $coordinate['lat'];
                $deliveryZonePoint->lng = $coordinate['lng'];
                $deliveryZonePoint->save();
            }

            DB::commit();
            $this->dismissModal();
            $this->reset();
            $this->showSuccessAlert(__("Delivery Zone") . " " . __('created successfully!'));
            $this->emit('refreshTable');
            $this->emit('resetMap');
        } catch (\Exception $ex) {
            DB::rollback();
            logger("Error", [$ex]);
            $this->showErrorAlert($ex->getMessage() ?? __("Delivery Zone") . " " . __('creation failed!'));
        }
    }

}
