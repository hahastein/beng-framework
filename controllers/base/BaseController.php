<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2018/8/15 1:04
 */

namespace bengbeng\framework\controllers\base;

use bengbeng\framework\base\Enum;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * 基础控制器
 * Class BaseController
 * @package bengbeng\framework\controllers\base
 */
class BaseController extends Controller
{

    const ACTION_PREFIX = 'ACTION_';

    /**
     * 公用用户ID (get) 参数为 user_id
     * @var string $user_id
     */
    protected $user_id;
    /**
     * 关键词 (get) 参数为 keyword
     * @var string $keyword
     */
    protected $keyword;

    /**
     * 逻辑 set为字符串的类名 get返回实际类
     * @var string|mixed $logic
     */
    protected $logic;

    /**
     * 私有变量
     * @var array $actions
     */
    private $actions = [];

    public function init()
    {
        parent::init();

        $this->user_id = \Yii::$app->request->get('user_id', 0);
        $this->keyword = \Yii::$app->request->get('keyword', '');

    }

    /**
     * 获取 Bll Class
     * @param string $logicName 逻辑类的名称，可以是单类，也可以是多层级类(例如：class或者namespace.class)
     * @param string $namespace 所用命名空间,默认为本命名空间下,可使用提供的枚举（枚举定义范围：Enum::NAMESPACE_*）
     * @return mixed|false 返回 Bll Class
     */
    protected function getLogicLayer($logicName, $namespace=''){

        if(empty($namespace)){
            $namespace = '\\bengbeng\\framework\\';
        }

        $namespace = $namespace.'logic\\';
        $logicName = $namespace.$logicName;

        return $this->execLogicLayer($logicName);

    }

    private function execLogicLayer($logicName){

        $logicNameArray = [];
        if(!is_array($logicName)){
            $logicNameArray[] = $logicName;
        }

        $logicModel = new \stdClass();
        foreach ($logicNameArray as $model){

            $className = str_replace('.', '\\', $model);
            $logic_front = '';

            $logic = explode('\\', $className);
            $logic = $logic[count($logic)- 1];
            if(strstr($model, '.')){
                $logic_front = $logic[count($logic) - 2];
            }
            $logic = strtolower($logic);
            $logic = strtolower(str_replace('bll', '', $logic));

            if(class_exists($className)){
                //处理不同命名空间下相同类名的问题
                if (isset($logicModel->$logic)){
                    $logic = $logic_front . '_' . $this->logic;
                    $logicModel->$logic = new $className;
                }else{
                    $logicModel->$logic = new $className;
                }
            }else{
                $logicModel->$logic = false;
            }

        }
        return $logicModel;
    }

    /**
     * 设定逻辑处理类
     * 例：传入类似\bengbeng\extend\logic\xxxBLL，返回全小写的xxx作为指向类
     * 调用为 $this->>logic->xxx->getList();
     * @param string $logic
     */
    public function setLogic($logic)
    {
        $this->logic = $this->execLogicLayer($logic);
    }

    protected function setActions($actions, $access = Enum::ACCESS_RULE_AUTHENTICATED){

        if(isset($this->actions[self::ACTION_PREFIX. $access])){
            $this->actions[self::ACTION_PREFIX. $access]['actions'] = ArrayHelper::merge($this->actions[self::ACTION_PREFIX. $access]['actions'], $actions);
        }else{
            $this->actions[self::ACTION_PREFIX. $access] = [
                'actions' => $actions,
                'allow' =>  true
            ];
            if($access != Enum::ACCESS_RULE_NULL){
                $this->actions[self::ACTION_PREFIX. $access]['roles'] = $access;
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
        if(isset($this->actions[self::ACTION_PREFIX . Enum::ACCESS_RULE_AUTHENTICATED])){
            $rules[] = self::setDefaultRules(ArrayHelper::merge($defaultActions, $this->actions[self::ACTION_PREFIX. Enum::ACCESS_RULE_AUTHENTICATED]['actions']));
        }else{
            $rules[] = self::setDefaultRules();
        }
        if(isset($this->actions[self::ACTION_PREFIX . Enum::ACCESS_RULE_NULL])){
            $rules[] = $this->actions[self::ACTION_PREFIX . Enum::ACCESS_RULE_NULL];
        }

        if(isset($this->actions[self::ACTION_PREFIX . Enum::ACCESS_RULE_GUEST])){
            $rules[] = $this->actions[self::ACTION_PREFIX . Enum::ACCESS_RULE_GUEST];
        }
        return self::setAccess($rules);
    }
}