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
        $cacheName = Enum::CACHE_USER_DATA.$unionID;
        if($getNew){
            return CacheHandle::get($cacheName, function () use ($unionID){
                return (new UserARModel())->findAllByUnionId($unionID)->toArray();
            });
        }else{
            return CacheHandle::get($cacheName);
        }
    }
}