<?php


namespace bengbeng\framework\components\handles;


class CacheHandle
{

    public static function get($name, \Closure $callback = null){

        $cache = \Yii::$app->cache;

        $cacheData = NULL;
        if($cache) {
            $cacheData = $cache->get($name);


            if ($cacheData === NULL && $callback) {
                $cacheData = call_user_func($callback);
                $cache->set($name, $cacheData);
            }

        }

        return $cacheData;

    }
}