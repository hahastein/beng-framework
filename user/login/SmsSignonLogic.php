<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\components\handles\SmsHandle;
use bengbeng\framework\models\SmsARModel;

class SmsSignonLogic extends SignonAbstract
{

    public $phone_num;
    public $sms_code;

    public function init()
    {
        parent::init();
        $this->phone_num = $this->phone_num?:\Yii::$app->request->post('phone_num', 0);
        $this->sms_code = $this->sms_code?:\Yii::$app->request->post('code', 0);

        $this->model = new SmsARModel();
        $this->userModel->setScenario('sms');
    }

    public function login()
    {
        try{
            if(!$this->phone_num || !$this->sms_code){
                throw new \Exception('手机号和短信验证码未填写');
            }
            //检查短信安全性
            if(!SmsHandle::validateSmsCode([
                'phone_num' => $this->phone_num, 'code' => $this->sms_code
            ])){
                throw new \Exception('验证码错误');
            }

            //获取用户信息
            $userInfo = $this->userModel->findByMobilenumber($this->phone_num);
            if(!$userInfo && !$this->isAutoReg){
                throw new \Exception('用户信息错误');
            }

            if($userInfo){
                //验证状态码
                //登录成功直接返回用户信息

            }else{
                //自动注册
                $this->saveUser();
                //返回执行码

            }



        }catch (\Exception $ex){

        }

    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }


}