<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/8/12
 * Time: 11:19
 */

namespace bengbeng\framework\components\helpers;


class LocationHelper
{
    const ERATH_RADIUS = 6378.137;

    /**
     * 计算两者之间的距离
     * @param $my_lat
     * @param $my_lng
     * @param $other_lat
     * @param $other_lng
     * @return float|int
     */
    public static function BetweenDistance($my_lat, $my_lng, $other_lat, $other_lng){
        $myLat = deg2rad($my_lat); //deg2rad()函数将角度转换为弧度
        $otherLat = deg2rad($other_lat);
        $myLng = deg2rad($my_lng);
        $otherLng = deg2rad($other_lng);
        $lat = $myLat - $otherLat;
        $lng = $myLng - $otherLng;
        $result = 2 * asin(sqrt(pow(sin($lat / 2), 2) + cos($myLat) * cos($otherLat) * pow(sin($lng / 2), 2))) * self::ERATH_RADIUS * 1000;
        return $result;
    }
}