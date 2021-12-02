<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Traits\FirebaseAuthTrait;

class VehicleObserver
{

    use FirebaseAuthTrait;

    public function created(Vehicle $vehicle)
    {
        $driver = $vehicle->driver;
        //sync vehicle type id
        //driver ref
        $driverRef = "drivers/" . $driver->id . "";
        $firestoreClient = $this->getFirebaseStoreClient();
        //
        try {
            $firestoreClient->addDocument(
                $driverRef,
                [
                    'vehicle_type_id' => (int) $vehicle->vehicle_type_id
                ]
            );
        } catch (\Exception $error) {
            try {
                $firestoreClient->updateDocument(
                    $driverRef,
                    [
                        'vehicle_type_id' => (int) $vehicle->vehicle_type_id
                    ]
                );
            } catch (\Exception $error) {
                logger("Dirver DATA update error", [$error]);
            }
        }
    }

    public function updated(Vehicle $vehicle)
    {
        $driver = $vehicle->driver;
        //sync vehicle type id
        //driver ref
        $driverRef = "drivers/" . $driver->id . "";
        $firestoreClient = $this->getFirebaseStoreClient();
        //
        try {
            $firestoreClient->addDocument(
                $driverRef,
                [
                    'vehicle_type_id' => (int) $vehicle->vehicle_type_id
                ]
            );
        } catch (\Exception $error) {
            try {
                $firestoreClient->updateDocument(
                    $driverRef,
                    [
                        'vehicle_type_id' => (int) $vehicle->vehicle_type_id
                    ]
                );
            } catch (\Exception $error) {
                logger("Dirver DATA update error", [$error]);
            }
        }
    }
}
