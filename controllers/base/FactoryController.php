<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/8/15
 * Time: 1:04
 */

namespace bengbeng\framework\controllers\base;


use bengbeng\framework\base\Enum;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * 后台基础控制器
 * Class FactoryController
 * @package bengbeng\framework\controllers\base
 */
class FactoryController extends Controller
{

    public $user_id;
    public $keyword;

    private $actions = [];

    public function init()
    {
        parent::init();

        $this->user_id = \Yii::$app->request->get('user_id', 0);
        $this->keyword = \Yii::$app->request->get('keyword', '');

    }

    protected function setActions($actions, $access = Enum::ACCESS_RULE_AUTHENTICATED){

        if($access == Enum::ACCESS_RULE_NULL){
            $this->actions[] = [
                'actions' => $actions,
                'allow' =>  true
            ];
        }else{
            $this->actions[] = [
                'actions' => $actions,
                'allow' =>  true,
                'roles' => $access
            ];
        }
    }

    public function behaviors()
    {
        return [
            'access' => self::mergeActionToAccess(),
            'verbs' => self::setDefaultVerbs()
        ];
    }

    /**
     * 私有方法
     * 设置默认权限控制，公共可访问actions 为 [all, info, modify, create]
     * @return array
     */
    private function setDefaultAccess(){
        return [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['all', 'info', 'modify', 'create'],
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ]
        ];
    }

    private function setDefaultVerbs(){
        return [
            'class' => VerbFilter::className(),
            'actions' => [
                'logout' => ['post'],
            ]
        ];
    }

    private function mergeActionToAccess(){
        $defaultAccess = self::setDefaultAccess();
        $customAccess = [
            'rules' => $this->actions
        ];
        return ArrayHelper::merge($defaultAccess, $customAccess);
    }
}