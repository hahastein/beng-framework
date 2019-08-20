<?php

namespace bengbeng\framework\base;

use bengbeng\framework\user\UserProperty;
use bengbeng\framework\user\UserUtil;
use yii\base\UnknownPropertyException;

/**
 * BengBeng Framework 功能引导程序
 * Class Bootstrap
 * @package bengbeng\framework\base
 */
class Bootstrap
{

    private $components;

    /**
     * @var integer $userID 用户ID
     */
    protected $userID;

    /**
     * @var string $unionID 用户唯一标识
     */
    protected $unionID;

    protected $moduleName;
    /**
     * @var UserProperty|false $user 用户信息
     */
    protected $user;

    public function __construct()
    {
        $unionID = \Yii::$app->request->post('unionid', '');
        if(!empty($unionID)){
            $this->setUnionID($unionID);
        }
        $this->userID = 0;
        $this->user = false;

        $this->init();
    }

    /**
     * 初始化
     */
    public function init(){}

    /**
     * 设置用户ID 当用户ID设置后
     * @param integer $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * 设置用户唯一标识
     * @param string $unionID
     */
    public function setUnionID($unionID)
    {
        $this->unionID = $unionID;

        if(!$this->userID){
            //如果没有userid，需要将unionid转换为userid
            $this->user = $this->unionToUser();
            if($this->user){
                $this->userID = $this->user->userID;
            }
        }
    }

    /**
     * 处理各个功能入口
     * @param $name
     * @return array
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {

        if(isset($this->components[$name])){
            return $this->components[$name];
        }else{
//            $getter = 'get' . $name;
            if (isset($this->moduleName) && !empty($this->moduleName)) {
                //class name
                $className = '\\bengbeng\\framework\\'.$this->moduleName.'\\'.ucfirst($name).'Logic';
                // read property, e.g. getName()
                return $this->components[$name] = $this->setProperty(new $className());
            }
            throw new UnknownPropertyException('没有找到此功能: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * 设置属性
     * @param $class
     * @return mixed
     */
    protected function setProperty($class){

        if(!method_exists($class,'setUserID')){
            $class->setUserID($this->userID);
        }

        if(!method_exists($class,'setUnionID')){
            $class->setUnionID($this->userID);
        }

        return $class;
    }

    /*
     * 以下是私有方法
     */

    /**
     * 获取用户后转换为OBJ
     * @return UserProperty|bool|NULL
     */
    private function unionToUser(){
        $userProperty = UserUtil::getCache($this->unionID);
        if($userProperty && isset($userProperty->userID)){
            return $userProperty;
        }
        return false;
    }
}