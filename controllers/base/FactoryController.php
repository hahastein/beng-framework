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

        if(isset($this->actions['a_'. $access])){
            $this->actions['a_'. $access]['actions'] = ArrayHelper::merge($this->actions['a_'. $access]['actions'], $actions);
        }else{
            $this->actions['a_'. $access] = [
                'actions' => $actions,
                'allow' =>  true
            ];
            if($access != Enum::ACCESS_RULE_NULL){
                $this->actions['a_'. $access]['roles'] = $access;
            }
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
     * @param null $rules
     * @return array
     */
    private function setAccess($rules){
        return [
            'class' => AccessControl::className(),
            'rules' => $rules
        ];
    }

    /**
     * @param null $actions
     * @return array
     */
    private function setDefaultRules($actions = null){
        return [
            'actions' => empty($actions)?self::setDefaultActions():$actions,
            'allow' => true,
            'roles' => ['@']
        ];
    }

    private function setDefaultActions(){
        return ['all', 'info', 'modify', 'create'];
    }

    private function setDefaultVerbs(){
        return [
            'class' => VerbFilter::className(),
            'actions' => [
                'logout' => ['get', 'post'],
            ]
        ];
    }

    private function mergeActionToAccess(){
        $rules = [];
        $defaultActions = self::setDefaultActions();
        if(isset($this->actions['a_' . Enum::ACCESS_RULE_AUTHENTICATED])){
            $rules[] = self::setDefaultRules(ArrayHelper::merge($defaultActions, $this->actions['a_'. Enum::ACCESS_RULE_AUTHENTICATED]['actions']));
        }else{
            $rules[] = self::setDefaultRules();
        }
        if(isset($this->actions['a_' . Enum::ACCESS_RULE_NULL])){
            $rules[] = $this->actions['a_' . Enum::ACCESS_RULE_NULL];
        }

        if(isset($this->actions['a_' . Enum::ACCESS_RULE_GUEST])){
            $rules[] = $this->actions['a_' . Enum::ACCESS_RULE_GUEST];
        }
        return self::setAccess($rules);
    }
}