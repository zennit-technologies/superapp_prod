<?php

namespace App\Http\Livewire;

use Exception;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\CommonMarkConverter;
use GeoSot\EnvEditor\Facades\EnvEditor;

class TaxiSettingLivewire extends BaseLivewireComponent
{

    // App settings
    public $cancelPendingTaxiOrderTime;
    public $drivingSpeed;
    public $pending;
    public $preparing;
    public $ready;
    public $enroute;
    public $completed;
    public $cancelled;
    public $failed;
    public $multipleCurrency;


    public function mount()
    {
        $this->cancelPendingTaxiOrderTime = setting('taxi.cancelPendingTaxiOrderTime', 2);
        $this->drivingSpeed = setting('taxi.drivingSpeed', 50);
        $this->pending = setting('taxi.msg.pending', "");
        $this->preparing = setting('taxi.msg.preparing', "");
        $this->ready = setting('taxi.msg.ready', "");
        $this->enroute = setting('taxi.msg.enroute', "");
        $this->completed = setting('taxi.msg.completed', "");
        $this->cancelled = setting('taxi.msg.cancelled', "");
        $this->failed = setting('taxi.msg.failed', "");
        $this->multipleCurrency = (bool) setting('taxi.multipleCurrency', false);
        
    }

    public function render()
    {
        return view('livewire.taxi.taxi_settings');
    }




    public function saveSettings()
    {


        try {

            $this->isDemo();

            $appSettings = [
                'taxi.cancelPendingTaxiOrderTime' =>  $this->cancelPendingTaxiOrderTime,
                'taxi.drivingSpeed' =>  $this->drivingSpeed,
                'taxi.msg.pending' =>  $this->pending,
                'taxi.msg.preparing' =>  $this->preparing,
                'taxi.msg.ready' =>  $this->ready,
                'taxi.msg.enroute' =>  $this->enroute,
                'taxi.msg.completed' =>  $this->completed,
                'taxi.msg.cancelled' =>  $this->cancelled,
                'taxi.msg.failed' =>  $this->failed,
                'taxi.multipleCurrency' =>  $this->multipleCurrency,
            ];

            // update the site name
            setting($appSettings)->save();



            $this->showSuccessAlert(__("App Settings saved successfully!"));
        } catch (Exception $error) {
            $this->showErrorAlert($error->getMessage() ?? __("App Settings save failed!"));
        }
    }
}
