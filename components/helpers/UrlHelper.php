<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/4/2 15:37
 */

namespace bengbeng\framework\components\helpers;

use yii\helpers\Url;

/**
 * Class UrlHelper
 * @author hahastein <146119@qq.com>
 * @package bengbeng\framework\components\helpers
 */
class UrlHelper
{
    /**
     * url跳转，如果$isAutoModule为True 则自动获取当前的Module的ID
     * @author hahastein <146119@qq.com>
     * @param string $url 要跳转的Url
     * @param bool $isAutoModule 是否获取当前的Module ID
     * @return string 返回新的Url
     */
    public static function to($url = '', $isAutoModule = true){
        if($isAutoModule){
            $moduleID = '/'.\Yii::$app->controller->module->id;
            $url = $moduleID.'/'.$url;
        }
        return Url::to([$url]);
    }

    public static function param($name, $defaultValue = null){
        if(\Yii::$app->request->isPost && \Yii::$app->request->post($name, $defaultValue)){
            return \Yii::$app->request->post($name, $defaultValue);
        }else{
            return \Yii::$app->request->get($name, $defaultValue);
        }
    }
}