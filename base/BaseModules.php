<?php

namespace bengbeng\framework\base;

use yii\base\UnknownPropertyException;

class BaseModules
{

    private $components;
    protected $moduleName;

    public function __construct()
    {
    }

    /**
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
//                var_dump($className);
                // read property, e.g. getName()
                return $this->components[$name] = $this->setProperty(new $className());
            }
            throw new UnknownPropertyException('没有找到此功能: ' . get_class($this) . '::' . $name);
        }
    }

    protected function setProperty($class){
        return $class;
    }

}