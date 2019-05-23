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
use yii\helpers\ArrayHelper;

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
        if(is_array($extend->extensions)){

            if ($this->extensions === null) {
                $file = \Yii::getAlias('@vendor/yiisoft/extensions.php');
                $this->extensions = is_file($file) ? include $file : [];
            }
            $this->extensions = ArrayHelper::merge($this->extensions, $extend->extensions);
        }
    }
}