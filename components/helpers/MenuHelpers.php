<?php
namespace bengbeng\framework\components\helpers;

use Yii;

/**
 * 左侧菜单功能类.
 * 创建者:hahastein
 * 创建时间:2018/1/22 3:16
 * Class MenuHelpers
 * @package common\bengbeng\base\helpers
 */
class MenuHelpers
{
    private static $menuList = [
        [
            'name'=>'快捷菜单',
            'icon'=>'',
            'url'=>'',
            'child'=>[
                [
                    'name'=>'数据统计',
                    'url'=>'site/index',
                ],[
                    'name'=>'快速上下架商品',
                    'url'=>'',
                ],[
                    'name'=>'盘点商品',
                    'url'=>'',
                ]
            ]
        ], [
            'name'=>'学校管理',
            'icon'=>'',
            'url'=>'',
            'child'=>[
                [
                    'name'=>'数据统计',
                    'url'=>'site/index',
                ],[
                    'name'=>'快速上下架商品',
                    'url'=>'',
                ],[
                    'name'=>'盘点商品',
                    'url'=>'',
                ]
            ]
        ], [
            'name'=>'配货仓管理',
            'icon'=>'',
            'url'=>'',
            'child'=>[
                [
                    'name'=>'数据统计',
                    'url'=>'site/index',
                ],[
                    'name'=>'快速上下架商品',
                    'url'=>'',
                ],[
                    'name'=>'盘点商品',
                    'url'=>'',
                ]
            ]
        ]
    ];

    public static function getContent(){
        $controllerID = Yii::$app->controller->id;
        $actionID = Yii::$app->controller->action->id;

        return MenuHelpers::recursion(self::$menuList);
    }

    private static function recursion($list){

        foreach ($list as $menu){

        }

        return '';
    }
}