<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\components\handles\SmsHandle;
use bengbeng\framework\models\SmsARModel;

class SmsSignonLogic extends SignonAbstract
{
    public function init()
    {
        parent::init();
        $this->model = new SmsARModel();
        $this->userModel->setScenario('sms');

    }

    public function login()
    {
        try{
            //检查短信安全性
            if(!SmsHandle::validateSmsCode([
                'phone_num' => '', 'code' => 123123
            ])){
                throw new \Exception('验证码错误');
            }

            $this->saveUser();

        }catch (\Exception $ex){

        }

    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }


}