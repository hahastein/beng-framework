<?php

namespace bengbeng\framework\base;

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
    /**
     * @param string $unionID
     */
    public function setUnionID($unionID)
    {
        $this->unionID = $unionID;
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

    protected function setProperty($class){

        if(!method_exists($class,'setUserID')){
            $class->setUserID($this->userID);
        }

        if(!method_exists($class,'setUnionID')){
            $class->setUnionID($this->unionID);
        }

        return $class;
    }

}