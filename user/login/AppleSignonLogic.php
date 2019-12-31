<?php


namespace bengbeng\framework\user\login;


class AppleSignonLogic extends SignonAbstract
{

    public $code;

    public function init()
    {
        parent::init();
        $this->code = \Yii::$app->request->post('apple_code', '');

    }

    public function login()
    {
        try {

            $this->userModel->showField = 'user_id,unionid, username, nickname,wx_bind, avatar_head, user_sex, user_extend, gps_lng, gps_lat, user_state';
            $userInfo = $this->userModel->info(['apple_user_id' => $this->code]);

            if($userInfo){
                $userInfo = $userInfo->toArray();

                //验证状态码
                if($userInfo['user_state'] === 0){
                    throw new \Exception('用户被禁止登录,请联系管理员');
                }else if($userInfo['user_state'] == 10){
                    $this->code = 4100;
                    throw new \Exception('用户未补全信息，请补全信息');
                }

                if($this->endLoginCallback){
                    $userInfo = call_user_func($this->endLoginCallback, $userInfo);
                }

                //登录成功直接返回用户信息
                $this->parseUserInfo($userInfo);
                return $userInfo;


            }else{
                $params['apple_code'] = $this->code;
                $this->returnData = $params;
                $this->code = 4101;
                throw new \Exception('用户未绑定手机，请去绑定手机');
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