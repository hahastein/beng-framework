<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/4/6 13:47
 */

namespace bengbeng\framework\components\helpers;


class ArrayHelper
{
    /**
     * 按key获取数组的内容并且从数组内移除key对应的内容
     * @param string $key 键值
     * @param array $newArray 移除后的新数组
     * @return string 返回key对应的内容
     */
    public static function returnKeyAndRemove($key, &$newArray){
        $keyContent = $newArray[$key];
        unset($newArray[$key]);
        return $keyContent;
    }
}