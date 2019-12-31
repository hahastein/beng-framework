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
        $this->sms_code = $this->sms_code?:\Yii::$app->request->post('sms_number', 0);

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
            if($this->notCheckSms && !SmsHandle::validateSmsCode([
                'phone_num' => $this->phone_num, 'code' => $this->sms_code
            ])){
                throw new \Exception('验证码错误');
            }

            //获取用户信息
            $this->userModel->showField = 'user_id, unionid, username, nickname, wx_bind, avatar_head, user_extend, user_sex, gps_lng, gps_lat, user_state';
            $userInfo = $this->userModel->findByMobilenumber($this->phone_num);

            if($userInfo){
                $userInfo = $userInfo->toArray();

                //验证状态码
                if($userInfo['user_state'] === 0){
                    throw new \Exception('用户被禁止登录,请联系管理员');
                }else if($userInfo['user_state'] == 10){
                    $this->code = 4100;
                    throw new \Exception('用户未补全信息，请补全信息');
                }
            }else{
                //如果设置为不是自动登录，则提示
                if(!$this->isAutoReg){
                    throw new \Exception('无此用户，请先注册');
                }

                $userInfo['phone_num'] = $this->phone_num;
                //自动注册并返回
                $this->saveUser();
                //返回执行码

            }

            if($this->endLoginCallback){
                var_dump($this->endLoginCallback);die;
                $this->endLoginCallback($userInfo);
            }

            //登录成功直接返回用户信息
            $this->parseUserInfo($userInfo);

            //更改短信状态
            SmsHandle::status($this->phone_num, $this->sms_code);


            return $userInfo;


        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }

    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }


}