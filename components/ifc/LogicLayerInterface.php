<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/3/31 3:04
 */

namespace bengbeng\framework\components\ifc;

/**
 * Interface LogicLayerInterface
 * @author hahastein <146119@qq.com>
 * @package bengbeng\admin\logic
 */
interface LogicLayerInterface
{
    /**
     * 获取数据集
     * @return mixed
     */
    public function getList();

    /**
     * 获取单条数据
     * @return mixed
     */
    public function getOne();
}