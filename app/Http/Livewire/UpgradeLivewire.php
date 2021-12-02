<?php

namespace App\Http\Livewire;

use App\Traits\SystemUpdateTrait;

class UpgradeLivewire extends BaseLivewireComponent
{

    use SystemUpdateTrait;
    public $terminalCommand;
    public $terminalError;
    public $phone_code;
    public $verison_code;


    public function render()
    {

        $this->verison_code = setting('appVerisonCode', "1");
        return view('livewire.settings.upgrade', [
            "verisonCodes" => $this->versionCodes(),
        ]);
    }

    public function versionCodes()
    {

        $appVersionCode = setting('appVerisonCode', "1");
        $verisons = [];
        $startCount = $appVersionCode - 3;
        for ($i = $startCount; $i <= $appVersionCode; $i++) {
            array_push($verisons, $i);
        }
        return $verisons;
    }

    public function upgradeAppSystem()
    {

        try {
            //
            $this->isDemo();
            //
            if ($this->verison_code <= 15) {
                $this->runUpgradeAppSystemCommands();
            } else {
                //
                $newVerisonCode = $this->verison_code + 1;
                $upgradeClassName = "App\\Upgrades\\Upgrade" . $newVerisonCode . "";
                if (!class_exists($upgradeClassName)) {
                    $this->showSuccessAlert(__("System Updated Successfully!").". v".$this->verison_code."");
                    return;
                }
                $upgradeClass = new $upgradeClassName();
                $upgradeClass->run();
                $upgradeClass->update();
            }
            $this->showSuccessAlert(__("System Updated Successfully!"));
        } catch (\Exception $ex) {
            $this->showErrorAlert($ex->getMessage() ?? __("Failed"));
        }
    }



    public function rollBackUpgrade()
    {
        try {
            //
            $this->isDemo();
            setting([
                'appVerisonCode' =>  $this->verison_code,
            ])->save();
            $this->runUpgradeAppSystemCommands();
            $this->showSuccessAlert(__("Rollback Updated Successfully!"));
        } catch (\Exception $ex) {
            $this->showErrorAlert($ex->getMessage() ?? __("Failed"));
        }
    }


    public function runTerminalCommand()
    {

        $this->terminalError = "";

        try {

            //
            $this->isDemo();
            $this->systemTerminalRun($this->terminalCommand);
            $this->showSuccessAlert(__("Terminal command successfully!"));
        } catch (\Exception $error) {
            $this->terminalError = $error->getMessage() ?? __("Terminal command failed!");
        }
    }
}
