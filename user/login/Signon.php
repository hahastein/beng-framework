<?php


namespace bengbeng\framework\user\login;

/**
 * 统一单点登录
 * Class Signon
 *
 * @package bengbeng\framework\user\login
 */
class Signon
{
    private $loginMode;

    /**
     * @var SignonAbstract $driver
     */
    private $driver;

    public function __construct($mode = 0)
    {
        //处理基础逻辑
        $mode = empty($mode)?0:$mode;
        $this->loginMode = (string)$mode;
        //转换类型
        $this->change();

        $this->init();
    }

    private function init(){

        $class = '\\bengbeng\\framework\\user\\login\\'.$this->loginMode;
        if(class_exists($class)){
            $this->driver = new $class();
        }else{
            $this->driver = false;
        }

    }

    public function login(\Closure $closure){
//        print_r($closure);

        call_user_func($closure, []);

        die;

        if($user = $this->driver->login()){
            return $user;
        }else{
            return false;
        }
    }



    public function getError(){
        return $this->driver->getError();
    }

    public function getCode()
    {
        return $this->driver->getCode();
    }

    public function setConfig($config){
        $this->driver->setConfig($config);
    }

    public function setEndLoginCallback(\Closure $closure){
        $this->driver->setEndLoginCallback($closure);
    }

    private function change(){

        $mode = [
            '0' => 'Sms',
            '10' => 'Pass',
            '20' => 'Wx',
            '25' => 'Apple'
        ];

        $this->loginMode = $mode[$this->loginMode].'SignonLogic';

    }

}