<?php

namespace App\Services;

use Aloha\Twilio\Twilio;
use App\Traits\FirebaseAuthTrait;
use Illuminate\Support\Facades\Http;
use Loafer\GatewayApi\GatewayApi;

class FirestoreRestService
{

    use FirebaseAuthTrait;
    public $authToken;

    public function refreshAuth($force = false)
    {
        $this->authToken = setting("serverFBAuthToken", "");
        $tokenExpiry = ((int) setting("serverFBAuthTokenExpiry", 0)) ?? 0;
        //
        if ($force || $tokenExpiry < now()->milliseconds) {
            $uId = "web_server";
            $firebaseAuth = $this->getFirebaseAuth();
            $customToken = $firebaseAuth->createCustomToken($uId);
            $signInResult = $firebaseAuth->signInWithCustomToken($customToken);
            $this->authToken = $signInResult->idToken();
            //generate new tokens 5mintues to its expiry
            $tokenExpiry = (now()->milliseconds + ($signInResult->ttl() ?? 0)) - 300000;
            //
            setting([
                "serverFBAuthToken" => $this->authToken,
                "serverFBAuthTokenExpiry" => $tokenExpiry,
            ])->save();
        }
    }
    public function whereBetween($earthDistanceToNorth, $earthDistanceToSouth, $rejectedDriversCount)
    {

        //
        $this->refreshAuth();
        //
        $maxDriverOrderNotificationAtOnce = (int) setting('maxDriverOrderNotificationAtOnce', 1) + $rejectedDriversCount;
        $baseUrl = "https://firestore.googleapis.com/v1/projects/" . setting("projectId", "") . "/databases/(default)/documents/:runQuery";

        // logger("Search range", [$earthDistanceToNorth, $earthDistanceToSouth, $maxDriverOrderNotificationAtOnce]);
        //
        $response = Http::withToken($this->authToken)->post(
            $baseUrl,
            [
                'structuredQuery' => [
                    'where' => [
                        'compositeFilter' => [
                            'op' => 'AND',
                            'filters' => [
                                0 => [
                                    'fieldFilter' => [
                                        'field' => [
                                            'fieldPath' => 'earth_distance',
                                        ],
                                        'op' => 'LESS_THAN_OR_EQUAL',
                                        'value' => [
                                            'doubleValue' => $earthDistanceToNorth,
                                        ],
                                    ],
                                ],
                                1 => [
                                    'fieldFilter' => [
                                        'field' => [
                                            'fieldPath' => 'earth_distance',
                                        ],
                                        'op' => 'GREATER_THAN_OR_EQUAL',
                                        'value' => [
                                            'doubleValue' => $earthDistanceToSouth,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'from' => [
                        0 => [
                            'collectionId' => 'drivers',
                        ],
                    ],
                    'limit' => $maxDriverOrderNotificationAtOnce,
                ],
            ]
        );

        //
        $drivers = [];
        if ($response->ok()) {
            $driversRawData = $response->json();
            foreach ($driversRawData as $driver) {
                if (empty($driver["document"])) {
                    continue;
                }
                //else get driver data
                try {
                    $drivers[] = [
                        "id" => $driver["document"]["fields"]["id"]["stringValue"],
                        "lat" => $driver["document"]["fields"]["lat"]["doubleValue"],
                        "long" => $driver["document"]["fields"]["long"]["doubleValue"],
                        "earth_distance" => $driver["document"]["fields"]["earth_distance"]["doubleValue"],
                    ];
                } catch (\Exception $ex) {
                    logger("Driver details Error", [$ex]);
                }
            }
        } else {
            $errorCode = $response->json()[0]["error"]["code"];
            if ($errorCode == 401 || $errorCode == 403) {
                $this->refreshAuth(true);
            }
            logger("Error with drivers search", [$response->body()]);
            // throw new \Exception($response->body(), 1);
        }

        return $drivers;
    }
}
