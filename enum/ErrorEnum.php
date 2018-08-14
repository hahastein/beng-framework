<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/8/15
 * Time: 1:14
 */

namespace bengbeng\framework\enum;

use bengbeng\framework\base\Enum;

class ErrorEnum extends Enum
{
    public static function infoChange($code = self::ERROR_CUSTOMER){
        switch ($code){
            case self::ERROR_NO_PARAMS:
                return "错误参数信息";
            case self::ERROR_NO_FIND_USER:
                return "没有找到用户信息";
            default:
                return "";
        }
    }
}