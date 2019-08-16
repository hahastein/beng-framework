<?php


namespace bengbeng\framework\system;


class SettingProperty
{

    public $appName;
    public $webName;
    public $cateVersion;
    public $cityVersion;

    public function __construct($setting)
    {

        var_dump($setting);die;
        foreach ($setting as $value){
            $key = $value['setting_name'];
            if(isset($this->$key)){
                $this->$key = $value['setting_value'];
            }
        }
    }

}