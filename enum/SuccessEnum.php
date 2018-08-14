<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/8/15
 * Time: 1:34
 */

namespace bengbeng\framework\enum;


use bengbeng\framework\base\Enum;

class SuccessEnum extends Enum
{
    public static function infoChange($code = self::SUCCESS_CUSTOMER){
        switch ($code){
            case self::SUCCESS_ADD_USER:
                return "添加用户成功";
            case self::SUCCESS_EDIT_USER:
                return "修改用户信息成功";
            default:
                return "";
        }
    }
}