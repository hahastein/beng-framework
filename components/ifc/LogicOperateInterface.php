<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/3/31 12:38
 */

namespace bengbeng\framework\components\ifc;

/**
 * Interface LogicOperateInterface
 * @author hahastein <146119@qq.com>
 * @package bengbeng\admin\logic
 */
interface LogicOperateInterface
{
    /**
     * 保存数据
     * @return mixed
     */
    public function save();

    /**
     * 删除单条数据
     * @return mixed
     */
    public function delete();
}