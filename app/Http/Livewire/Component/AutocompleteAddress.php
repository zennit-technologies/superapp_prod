<?php

namespace App\Http\Livewire\Component;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AutocompleteAddress extends Component
{

    public $title;
    public $name;
    public $addresses = [];
    public $address;

    protected $listeners = [
        'initialAddressSelected' => 'initialAddressSelected',
        'addressSelected' => 'addressSelected',
    ];



    public function render()
    {
        return view('livewire.component.autocomplete-address');
    }

    public function updatedAddress()
    {
        if(!empty($this->address)){
        $this->googlePlaces();
        }
    }
    //call google place api key to give you result base on when you entered
    private function googlePlaces()
    {

        $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
            "input" => $this->address,
            "key" => setting("googleMapKey", ""),
        ]);
        $this->addresses = [];

        if ($response->successful()) {
            $predictions = $response->json()["predictions"];
            foreach ($predictions as $prediction) {
                array_push($this->addresses, [
                    "name" => $prediction["description"],
                    "id" => $prediction["place_id"],
                ]);
            }
        }

        //emit raw data enatered by user
        if (empty($this->addresses)) {
            $fullAddressData = [
                "address" => $this->address,
                "latitude" =>  0.00,
                "longitude" => 0.00,
                "city" => "",
                "state" => "",
                "country" => "",
            ];

            $this->emitUp('autocompleteAddressSelected', $fullAddressData);
        }
    }

    public function initialAddressSelected($address)
    {

        $this->address = $address;
    }

    public function addressSelected($selectedIndex)
    {


        if (!array_key_exists($selectedIndex, $this->addresses)) {
            return;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
            "placeid" => $this->addresses[$selectedIndex]["id"],
            "key" => setting("googleMapKey", ""),
        ]);
        $this->addresses = [];

        if ($response->successful()) {


            $city = "";
            $state = "";
            $country = "";

            $addressComponents = $response->json()["result"]["address_components"];
            //
            foreach ($addressComponents as $key => $addressComponent) {
                //country
                if (in_array("country", $addressComponent["types"])) {
                    $country = $addressComponent["long_name"];
                }
                //state
                else if (in_array("administrative_area_level_1", $addressComponent["types"])) {
                    $state = $addressComponent["long_name"];
                }
                //city
                else if (in_array("locality", $addressComponent["types"])) {
                    $city = $addressComponent["long_name"];
                }
            }

            $this->address = $response->json()["result"]["formatted_address"];
            $fullAddressData = [
                "address" => $this->address,
                "latitude" => $response->json()["result"]["geometry"]["location"]["lat"],
                "longitude" => $response->json()["result"]["geometry"]["location"]["lng"],
                "city" => $city,
                "state" => $state,
                "country" => $country,
            ];

            $this->emitUp('autocompleteAddressSelected', $fullAddressData);
        } else {
            // emit error to view
        }
    }
}
