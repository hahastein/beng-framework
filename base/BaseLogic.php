<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/5/21 22:37
 */

namespace bengbeng\framework\base;

class BaseLogic
{

    /**
     * 错误信息
     * @var string
     */
    public $error;

    /**
     * 数据模型
     * @var BaseActiveRecord
     */
    protected $model;

    /**
     * 创建数据模型
     * @param $model
     */
    protected function createModel($model = null)
    {
        $this->model = $model;
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}