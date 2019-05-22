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

    public $error;
    protected $model;

    public function __construct()
    {

        var_dump(get_class());die;

        //创建Model
        $this->createModel();

    }

    private function createModel(){

    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}