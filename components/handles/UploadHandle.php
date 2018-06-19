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

    const UPLOAD_TYPE_LOCAL = 1;
    const UPLOAD_TYPE_UPYUN = 2;

    private $uploadType;

    public function __construct($type)
    {
        $this->uploadType = $type;
    }

    public function save(){

        switch ($this->uploadType){
            case self::UPLOAD_TYPE_LOCAL:

                $this->local();
                break;
            case self::UPLOAD_TYPE_UPYUN:

                $this->upyun();
                break;
            default:

                break;
        }
    }

    private function local(){

    }

    /**
     *
     */
    private function upyun(){

    }


//    public function __set($key, $value)
//    {
//        $this->$key = $value;
//    }
//
//    public function __get($key){
//        if (isset($key)){
//            return $this->$key;
//        }else {
//            return NULL;
//        }
//    }
}