<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\CacheHandle;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\models\UserTokenARModel;

class UserUtil
{
    /**
     * 获取缓存用户信息
     * @param $unionID
     * @param bool $getNew
     * @return UserProperty|NULL
     */
    public static function getCache($unionID, $getNew = true){
        if(empty($unionID)){
            return NULL;
        }
        $cacheName = Enum::CACHE_USER_DATA.$unionID;
        if($getNew){
            return CacheHandle::get($cacheName, function () use ($unionID){
                return (new UserARModel())->findAllByUnionId($unionID);

            });
        }else{
            return CacheHandle::get($cacheName);
        }
    }

    public static function getUserIDByImID($imID){
        if($token = (new UserTokenARModel())->findByImID($imID)){
            return $token['user_id'];
        }
        return false;
    }
}