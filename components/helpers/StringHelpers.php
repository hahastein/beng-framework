<?php

namespace bengbeng\framework\components\helpers;

use Yii;

class StringHelpers
{

    public static $signSecret = "bengbeng@2017";

    public static function getPercent2($num1, $num2) {
        if($num2 == 0){
            return 0;
        }
        $num = ( $num1 / $num2 ) * 100;
        return $num;
    }

    /**
     * 生成随机字符串
     * @param int $len
     * @return string
     */
    public static function genRandomString($len)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars); // 将数组打乱
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars [mt_rand(0, $charsLen)];
        }
        return $output;
    }

    function genNumberAndUpper($length = 8)
    {
        $key = '';
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,strlen($pattern))};    //生成php随机数
        }
        return $key;
    }

    public static function createSign($param){

        // 1. 对加密数组进行字典排序
        foreach ($param as $key=>$value){
            $arr[$key] = $key;
        }
        sort($arr);

        $str = "";
        foreach ($arr as $k => $v) {
            $str = $str.$arr[$k].$param[$v];
        }

        //通过sha1加密并转化为大写
        //大写获得签名
        $restr=$str.self::$signSecret;
        $sign = strtoupper(sha1($restr));

        return $sign;

    }

}