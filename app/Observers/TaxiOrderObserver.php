<?php

namespace App\Observers;

use App\Models\Order;
use App\Traits\FirebaseAuthTrait;

class TaxiOrderObserver
{

    use FirebaseAuthTrait;

    public function updated(Order $model)
    {

        $driver = $model->driver;
        //update driver node on firebase 
        if (!empty($driver)) {
            //update driver free record on firebase
            //driver ref
            $driverRef = "drivers/" . $driver->id . "";
            $firestoreClient = $this->getFirebaseStoreClient();
            //
            try {
                $firestoreClient->addDocument(
                    $driverRef,
                    [
                        'free' => $driver->assigned_orders == 0 ? 1 : 0
                    ]
                );
            } catch (\Exception $error) {
                try {
                    $firestoreClient->updateDocument(
                        $driverRef,
                        [
                            'free' => $driver->assigned_orders == 0 ? 1 : 0
                        ]
                    );
                } catch (\Exception $error) {
                    logger("Dirver DATA update error", [$error]);
                }
            }
        }

        //
        $this->clearFirestore($model);
    }


    public function clearFirestore(Order $order)
    {
        //
        $canClearFirestore = (bool) setting('clearFirestore', 1);
        //
        if (in_array($order->status, ['failed', 'cancelled', 'delivered', 'completed']) && $canClearFirestore) {
            $firestoreClient = $this->getFirebaseStoreClient();
            $firestoreClient->deleteDocument("orders/" . $order->code . "");
        }
    }
}
