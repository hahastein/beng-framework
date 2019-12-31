<?php


namespace bengbeng\framework\user\login;


use bengbeng\framework\components\handles\SmsHandle;
use bengbeng\framework\components\handles\UserHandle;
use bengbeng\framework\models\SmsARModel;

class BindPhoneSignonLogic extends SignonAbstract
{

    public $phone_num;
    public $sms_code;
    public $mode;

    //微信的数据
    public $openid;
    public $unioncode;

    //苹果的数据
    public $apple_code;

    //第三方的用户信息
    public $avatar;
    public $sex;
    public $nickname;


    public function init()
    {
        parent::init();

        $this->phone_num = $this->phone_num?:\Yii::$app->request->post('phone_num', 0);
        $this->sms_code = $this->sms_code?:\Yii::$app->request->post('sms_number', 0);
        $this->mode = \Yii::$app->request->post('mode', '');


        $this->openid = \Yii::$app->request->post('openid', '');
        $this->unioncode = \Yii::$app->request->post('unioncode', '');

        $this->apple_code = \Yii::$app->request->post('apple_code', '');

        $this->avatar = \Yii::$app->request->post('avatar', '');
        $this->sex = \Yii::$app->request->post('sex', 0);
        $this->nickname = \Yii::$app->request->post('nickname', '');


        $this->model = new SmsARModel();
        $this->userModel->setScenario('sms');
    }

    public function login()
    {
        try {
            if(!$this->phone_num || !$this->sms_code){
                throw new \Exception('手机号和短信验证码未填写');
            }
            //检查短信安全性
            if($this->notCheckSms && !SmsHandle::validateSmsCode([
                    'phone_num' => $this->phone_num, 'code' => $this->sms_code
                ])){
                throw new \Exception('验证码错误');
            }

            $this->userModel->showField = 'user_id, unionid, username, nickname, wx_bind, avatar_head, user_sex, user_extend, gps_lng, gps_lat, user_state';

            if($this->mode == 20){
                $where = ['unionid' => $this->unioncode];
            }else if($this->mode == 25){
                $where = ['apple_user_id' => $this->apple_code];
            }else{
                throw new \Exception('无此类型');
            }

            //验证第三方是否存在
            $userInfo = $this->userModel->info($where);
            if($userInfo){
                throw new \Exception('此账号被绑定过，请更换第三方登录账号');
            }


            $saveParams = [
                'phone' => $this->phone_num,
                'unioncode' => $this->unioncode,
                'openid' => $this->openid,
                'apple_code' => $this->apple_code,
                'avatar' => $this->avatar,
                'nickname' => $this->nickname,
                'sex' => $this->sex
            ];
            if($this->saveUserParams){
                foreach ($this->saveUserParams as $key => $value){
                    $saveParams[$key] = $value;
                }
            }
            if($userInfo = UserHandle::bindRegister($this->mode,$saveParams, $isComplete)){
                if($isComplete){
                    $this->code = 4100;
                    $this->returnData = $userInfo;
                    throw new \Exception('用户未补全信息，请补全信息');
                }else{
                    $this->returnData = $userInfo;
                }

            }

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