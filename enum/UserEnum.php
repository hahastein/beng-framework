<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/23
 * Time: 0:56
 */

namespace bengbeng\framework\enum;


use bengbeng\framework\base\Enum;

class UserEnum extends Enum
{

    const USER_SEX_MALE = 1;
    const USER_SEX_FEMALE = 2;
    const USER_SEX_UNKNOWN = 0;

    /**
     * 性别转换
     * @param $sex
     * @return string
     */
    public static function sexChange($sex){
        switch ($sex){
            case self::USER_SEX_MALE:
                return "男";
            case self::USER_SEX_FEMALE:
                return "女";
            default:
                return "未知";
        }
    }

    const USER_PHONE_BIND = 1;
    const USER_PHONE_UNBIND = 0;

    const USER_WEIXIN_BIND = 1;
    const USER_WEXIN_UNBIND = 0;
}