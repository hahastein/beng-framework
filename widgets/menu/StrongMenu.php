<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-09-10
 * Time: 18:06
 */

namespace bengbeng\framework\widgets\menu;

use Yii;
use yii\base\Widget;

class StrongMenu extends Widget
{
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function run()
    {
        $controllerID = Yii::$app->controller->id;
        $actionID = Yii::$app->controller->action->id;

        return $this->render('menu-layout', [
            'controllerID'=>$controllerID,
            'actionID'=>$actionID
        ]);
    }
}