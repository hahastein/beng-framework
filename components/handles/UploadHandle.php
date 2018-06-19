<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/19
 * Time: 23:49
 */

namespace bengbeng\framework\components\handles;

class UploadHandle
{

    public function __construct()
    {

    }


    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __get($key){
        if (isset($key)){
            return $this->$key;
        }else {
            return NULL;
        }
    }
}