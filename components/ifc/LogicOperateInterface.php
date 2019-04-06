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
     * @param array $dataParam 存入的数据
     * @return mixed
     */
    public function save($dataParam = null);

    /**
     * 删除数据
     * @param mixed $id 要删除的ID，支持多个ID删除。(例：单ID $id = 1，多ID $id = [1,2,3])
     * @return mixed
     */
    public function delete($id);
}