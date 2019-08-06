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

        $cacheData = NULL;

        if($cache) {
            $cacheData = $cache->get($name);

            if (!$cacheData && $callback) {
                $cacheData = call_user_func($callback);
                $cache->set($name, $cacheData);
            }

            $userProperty = $cacheData?new UserProperty($cacheData):false;
        }else{
            $userProperty = false;
        }

        return $userProperty;

    }
}