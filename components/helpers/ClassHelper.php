<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/5/28 21:52
 */

namespace bengbeng\framework\components\helpers;


class ClassHelper
{

    /**
     * 返回指定的扩展库完整类地址
     * @param $extendName
     * @param $className
     * @return string
     */
    public static function extendNamespaceSplicing($extendName, $className){

        return '\\bengbeng\\extend\\' . $extendName . '\\logic\\' . $className;

    }
}