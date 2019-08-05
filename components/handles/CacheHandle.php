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

        if ($cacheData === false) {
            if($callback){
                var_dump($callback);die;
                $cacheData = call_user_func($callback);
            }
        }

        if($cache) {
            $cache->set($name, $cacheData);
        }

        return $cacheData;

    }
}