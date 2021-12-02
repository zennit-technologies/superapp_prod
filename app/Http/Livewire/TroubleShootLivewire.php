<?php

namespace App\Http\Livewire;

use GeoSot\EnvEditor\Facades\EnvEditor;

class TroubleShootLivewire extends BaseLivewireComponent
{


    public function render()
    {
        return view('livewire.troubleshoot');
    }


    public function fixImage()
    {

        try {
            //set the domain
            $url = url('');
            $envUrl = env('APP_URL');
            //
            if ($url != $envUrl) {
                if (EnvEditor::keyExists("APP_URL")) {
                    EnvEditor::editKey("APP_URL", $url);
                } else {
                    EnvEditor::addKey("APP_URL", $url);
                }
            }

            //artisan storage link
            \Artisan::call('storage:link', []);
            $this->showSuccessAlert(__("Fix Image(Not Loading)")." ".__("Successfully"));
        } catch (\Exception $ex) {
            $this->showErrorAlert($ex->getMessage() ?? __("Failed"));
        }
    }

    public function fixCache()
    {

        try {
            //artisan calls
            \Artisan::call('view:clear', []);
            \Artisan::call('config:clear', []);
            \Artisan::call('cache:clear', []);
            $this->showSuccessAlert(__("Clear Cache")." ".__("Successfully"));
        } catch (\Exception $ex) {
            $this->showErrorAlert($ex->getMessage() ?? __("Failed"));
        }
    }
}
