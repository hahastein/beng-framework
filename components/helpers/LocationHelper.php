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
    const PI = 3.1415926535898;
    const LOCATION_UNIT_AUTO = 1;
    const LOCATION_UNIT_KM = 2;
    const LOCATION_UNIT_M = 3;

    /**
     * 计算两者之间的距离
     * @param $my_lat
     * @param $my_lng
     * @param $other_lat
     * @param $other_lng
     * @param $unit
     * @return float|int
     */
    public static function BetweenDistance($my_lat, $my_lng, $other_lat, $other_lng, $unit = self::LOCATION_UNIT_AUTO){
        $myLat = $my_lat * self::PI / 180.0;
        $otherLat = $other_lat * self::PI / 180.0;

        $lat = $myLat - $otherLat;
        $lng = ($my_lng * self::PI / 180.0) - ($other_lng * self::PI / 180.0);

        $result = 2 * asin(sqrt(pow(sin($lat / 2), 2) + cos($myLat) * cos($otherLat) * pow(sin($lng / 2), 2)));
        $result = $result * self::ERATH_RADIUS;

        if($unit == self::LOCATION_UNIT_M){
            $result = round($result * 1000);
        }

        return $result;
    }
}