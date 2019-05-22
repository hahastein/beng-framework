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


use yii\base\Model;

class BaseLogic
{

    public $error;

    /**
     * @var Model
     */
    protected $model;

    protected function createModel($model = null)
    {
        $this->models = $model;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}