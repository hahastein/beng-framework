<?php


namespace bengbeng\framework\components\handles;


use bengbeng\framework\user\UserProperty;

class CacheHandle
{

    /**
     * @param $name
     * @param \Closure|null $callback
     * @return UserProperty|NULL
     */
    public static function get($name, \Closure $callback = null){

        $cache = \Yii::$app->cache;
$cache->delete($name);
        $userProperty = NULL;
        if($cache) {
            $userProperty = $cache->get($name);


//            var_dump( $cache->get($name));

            if ($userProperty === NULL && $callback) {
                $cacheData = call_user_func($callback);

                $userProperty = new UserProperty();
                $userProperty->userID = $cacheData['user_id'];
                $userProperty->unionID = $cacheData['unionid'];
                $userProperty->userName = $cacheData['username'];
                $userProperty->nickname = $cacheData['nickname'];
                $userProperty->userSex = $cacheData['user_sex'];
                $userProperty->userState = $cacheData['user_state'];
                $userProperty->phone = $cacheData['phone_num'];
                $userProperty->phoneBind = $cacheData['phone_bind'];
                $userProperty->wxBind = $cacheData['wx_bind'];
                $userProperty->wxOpenid = $cacheData['wx_openid'];
                $userProperty->avatarHead = $cacheData['avatar_head'];

                $cache->set($name, $userProperty);
            }

        }

        return $userProperty;

    }
}