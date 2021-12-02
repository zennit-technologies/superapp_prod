<?php

namespace App\Upgrades;


class BaseUpgrade
{

    public $versionName = "";
    
    public function update()
    {
        //
        $versionCode = setting('appVerisonCode', "1") + 1;

        setting([
            'appVerisonCode' =>  $versionCode,
            'appVerison' =>  $this->versionName,
        ])->save();
    }

    function fileEditContents($file_name, $line, $new_value)
    {
        $file = explode("\n", rtrim(file_get_contents($file_name)));
        $file[$line] = $new_value;
        $file = implode("\n", $file);
        file_put_contents($file_name, $file);
    }
}
