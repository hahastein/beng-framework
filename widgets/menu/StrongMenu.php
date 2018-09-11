<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-09-10
 * Time: 18:06
 */

namespace bengbeng\framework\widgets\menu;

use bengbeng\framework\models\platform\MenuARModel;
use Yii;
use yii\base\Widget;
use yii\db\ActiveQuery;

class StrongMenu extends Widget
{

    const TYPE_DEFAULT = 0;
    const TYPE_LEFT = 10;
    const TYPE_TOP = 20;
    const TYPE_RIGHT = 30;

    public $type = self::TYPE_DEFAULT;
    public $cache = true;

    private $menuData;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->menuData = [];
        self::initData();
    }

    private function initData(){
        $cache = Yii::$app->cache;

        $cache_data = false;

        Yii::$app->Beng->outHtml($this->cache); die;

        if($this->cache){
            $cache_data = $cache->get('system_menu_data');
        }

        if ($cache_data === false){
            $menuModel = new MenuARModel();
            $menuModel->showPage = false;
            $cache_data = $menuModel->dataSet(function (ActiveQuery $query){
                $query->select(['menu_name', 'menu_icon', 'module', 'controller', 'action', 'parent_id']);
                $query->where(['menu_type' => $this->type]);
                $query->asArray();
            });
            self::resetMenuData($cache_data);

            if($this->cache) {
                $cache->set('system_menu_data', $this->menuData);
            }
        }
    }

    public function run()
    {
        $moduleID = Yii::$app->module->id;
        $controllerID = Yii::$app->controller->id;
        $actionID = Yii::$app->controller->action->id;

        Yii::$app->Beng->outHtml($this->menuData); die;

        return $this->render('menu-'.self::changeType($this->type), [
            'controllerID' => $controllerID,
            'actionID' => $actionID,
            'moduleID' => $moduleID
        ]);
    }

    private function resetMenuData($menuData){
        foreach ($menuData as $menu){
            if($menu['parent_id'] == 0){
                $this->menuData[$menu['menu_id']] = $menu;
            }else{
                $this->menuData[$menu['parent_id']]['parent'][] = $menu;
            }
        }
    }

    public static function changeType($type){
        switch ($type){
            case self::TYPE_LEFT:
                return 'left';
            case self::TYPE_RIGHT:
                return 'right';
            case self::TYPE_TOP:
                return 'top';
            default:
                return 'layout';
        }
    }
}