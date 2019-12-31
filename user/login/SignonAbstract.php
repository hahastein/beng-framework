<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\models\UserARModel;
use yii\db\Exception;

abstract class SignonAbstract
{

    protected $error;
    protected $code;

    protected $model;
    protected $userModel;

    protected $saveCall;

    protected $isAutoReg;
    protected $notCheckSms;


    public function __construct($config = [])
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

    /**
     * 保存用户信息基础类
     * @return bool
     * @throws Exception
     */
    protected function saveUser(){

        $trans = \Yii::$app->db->beginTransaction();
        try {


            if($this->saveCall){

            }

            $trans->commit();
            return true;

        }catch (Exception $ex){

            $trans->rollBack();

            throw $ex;
        }


    }

    /**
     * 处理返回的用户信息
     * @param $userInfo
     */
    protected function parseUserInfo(&$userInfo){


        unset($userInfo['$userInfo']);
    }

    public function getError(){
        return $this->error;
    }

    public function getCode(){
        return $this->code;
    }

    private function settingConfig($config){

        $this->isAutoReg = false;
        if(isset($config['isAutoReg']) && is_bool($config['isAutoReg'])){
            $this->isAutoReg = $config['isAutoReg'];
        }

        var_dump(is_bool($config['checkSms']));die;

        $this->notCheckSms = true;
        if(isset($config['checkSms']) && is_bool($config['checkSms'])){
            $this->notCheckSms = $config['checkSms'];
        }
    }



//    function bind
}