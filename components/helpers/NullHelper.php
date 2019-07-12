<?php


namespace bengbeng\framework\components\helpers;

/**
 * Class NullHelper
 * @package bengbeng\framework\components\helpers
 */
class NullHelper
{
    /**
     * @param $array
     * @param $key
     * @return bool|mixed
     */
    public static function arrayKey($array, $key){

        if(array_key_exists($key, $array)){
            return $array[$key];
        }else{
            return false;
        }
    }
}