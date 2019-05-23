<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/5/23 15:21
 */

namespace bengbeng\framework\base;

use bengbeng\framework\components\handles\ExtendHandle;

class Application extends \yii\web\Application
{

    public function init()
    {
        //获取缓存的扩展内容
        $this->getExtend();
        parent::init();
    }

    private function getExtend(){

        $extend = new ExtendHandle();
        $extend->createCache();

    }
}