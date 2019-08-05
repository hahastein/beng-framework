<?php


namespace bengbeng\framework\components\handles;


class CacheHandle
{

    public static function get($name, \Closure $callback = null){

        $cache = \Yii::$app->cache;

        $cacheData = false;
        if($cache){
            $cacheData = $cache->get($name);
        }
        var_dump($cacheData);die;

        if ($cacheData === false) {
            if($callback){
                $cacheData = call_user_func($callback);
            }
        }

        if($cache) {
            $cache->set($name, $cacheData);
        }

        return $cacheData;

    }
}