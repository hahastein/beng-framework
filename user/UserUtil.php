<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\CacheHandle;
use bengbeng\framework\models\UserARModel;

class UserUtil
{
    /**
     * @param $unionID
     * @param bool $getNew
     * @return UserProperty|NULL
     */
    public static function getCache($unionID, $getNew = true){
        if($getNew){
            return CacheHandle::get(Enum::CACHE_USER_DATA, function () use ($unionID){
                return (new UserARModel())->findAllByUnionId($unionID);
            });
        }else{
            return CacheHandle::get(Enum::CACHE_USER_DATA);
        }
    }
}