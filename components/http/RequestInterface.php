<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/8/5 0:15
 */

namespace bengbeng\framework\components\http;


interface RequestInterface
{
    function http($url, $data, $header);
    function getError();
}