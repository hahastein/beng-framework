<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\models\UserARModel;

abstract class SignonAbstract
{

    protected $model;
    protected $userModel;

    protected $saveCall;

    protected $isAutoReg;

    public function __construct($config)
    {
        //设置登录配置
        $this->settingConfig($config);
        //初始化各Model
        $this->userModel = new UserARModel();
        $this->init();
    }

    /**
     * 子类继承的初始化方法
     */
    public function init(){}

    abstract function login();

    abstract function logout();

    public function saveUser(){


        if($this->saveCall){

        }
    }

    private function settingConfig($config){

        $this->isAutoReg = false;
        if(isset($config['isAutoReg']) && is_bool($config['isAutoReg'])){
            $this->isAutoReg = $config['isAutoReg'];
        }
    }



//    function bind
}