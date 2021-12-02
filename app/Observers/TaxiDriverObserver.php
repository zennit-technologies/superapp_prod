<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\FirebaseAuthTrait;

class TaxiDriverObserver
{

    use FirebaseAuthTrait;

    public function updated(User $user)
    {

        //drver update related
        //update driver node on firebase 
        if ($user->hasRole('driver')) {
            //update driver online record on firebase
            if ($user->isDirty('is_online')) {
                //driver ref
                $driverRef = "drivers/" . $user->id . "";
                $firestoreClient = $this->getFirebaseStoreClient();
                //
                try {
                    $firestoreClient->addDocument(
                        $driverRef,
                        [
                            'online' => (int) $user->is_online,
                            'free' => $user->assigned_orders == 0 ? 1 : 0
                        ]
                    );
                } catch (\Exception $error) {
                    try {
                        $firestoreClient->updateDocument(
                            $driverRef,
                            [
                                'online' => (int) $user->is_online,
                                'free' => $user->assigned_orders == 0 ? 1 : 0
                            ]
                        );
                    } catch (\Exception $error) {
                        logger("New Docus error", [$error]);
                    }
                }
            }
        }
    }
}
