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
    const ERATH_RADIUS = 6371.0;
    const PI = 3.1415926535898;
    const LOCATION_UNIT_AUTO = 1;
    const LOCATION_UNIT_KM = 2;
    const LOCATION_UNIT_M = 3;


    public static function TransformDistance($distance, $degree = false, $unit = self::LOCATION_UNIT_AUTO){
        if($degree){
            $distance = $distance * self::ERATH_RADIUS * self::PI / 180.0;
        }

        $result = $distance;

        if($unit == self::LOCATION_UNIT_M){
            $result = round($result * 1000, 0) .'米';
        }else if($unit == self::LOCATION_UNIT_AUTO){
            if($result<1) {
                $result = round($result * 1000, 0);
                if($result <= 50){
                    $result = '50米内';
                }else{
                    $result = $result . 'm';
                }
            }else{
                $result = round($result, 2). ' km';
            }
        }else{
            $result = round($result, 2). ' km';
        }

        return $result;
    }

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

        if($my_lat == 0 || $my_lng == 0 || $other_lat == 0 || $other_lng == 0){
            return '暂无距离';
        }

        $myLat = deg2rad($my_lat);
        $otherLat = deg2rad($other_lat);

        $myLng = deg2rad($my_lng);
        $otherLng = deg2rad($other_lng);

        $lat = $myLat - $otherLat;
        $lng = $myLng - $otherLng;

        $result = 2 * asin(sqrt(pow(sin($lat / 2), 2) + cos($myLat) * cos($otherLat) * pow(sin($lng / 2), 2)));
        $result = $result * self::ERATH_RADIUS;

        if($unit == self::LOCATION_UNIT_M){
            $result = round($result * 1000, 0) .'米';
        }else if($unit == self::LOCATION_UNIT_AUTO){
            if($result<1) {
                $result = round($result * 1000, 0);
                if($result <= 50){
                    $result = '50米内';
                }else{
                    $result = $result . 'm';
                }
            }else{
                $result = round($result, 2). ' km';
            }
        }else{
            $result = round($result, 2). ' km';
        }

        return $result;
    }
}