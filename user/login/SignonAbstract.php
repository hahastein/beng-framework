<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\UserHandle;
use bengbeng\framework\models\UserARModel;

abstract class SignonAbstract
{

    protected $error;
    protected $code;
    protected $returnData;

    protected $model;
    protected $userModel;

    protected $saveCall;

    protected $isAutoReg;
    protected $notCheckSms;

    /**
     * @var \Closure $endLoginCallback
     */
    protected $endLoginCallback;

    protected $saveUserParams;


    public function __construct()
    {
        //初始化各Model
        $this->userModel = new UserARModel();
        //设置要返回的数据字段
        $this->userModel->showField = 'user_id, unionid, username, nickname, wx_bind, avatar_head, user_sex, user_extend, gps_lng, gps_lat, is_auth, user_state';
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
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function saveUser($params){

//        $trans = \Yii::$app->db->beginTransaction();
        try {

            return UserHandle::register($params, Enum::REG_TYPE_APP);

//            if($this->saveCall){
//
//            }

//            $trans->commit();

        }catch (\Exception $ex){

//            $trans->rollBack();

            throw $ex;
        }


    }

    /**
     * 处理返回的用户信息
     * @param $userInfo
     */
    protected function parseUserInfo(&$userInfo){

        //移除用户ID
        unset($userInfo['user_id']);
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    public function getCode(){
        return $this->code;
    }

    public function getReturnData(){
        return $this->returnData;
    }

    public function setConfig($config){
        //设置登录配置
        $this->settingConfig($config);
    }

    public function setEndLoginCallback(\Closure $closure){
        $this->endLoginCallback = $closure;
    }

    public function setSaveUserParams($params){

        $this->saveUserParams = $params;

    }

    private function settingConfig($config){

        $this->isAutoReg = false;
        if(isset($config['isAutoReg']) && is_bool($config['isAutoReg'])){
            $this->isAutoReg = $config['isAutoReg'];
        }

        $this->notCheckSms = true;
        if(isset($config['checkSms']) && is_bool($config['checkSms'])){
            $this->notCheckSms = $config['checkSms'];
        }
    }



//    function bind
}