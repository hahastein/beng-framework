<?php


namespace bengbeng\framework\user\login;


class AppleSigonLogic extends SignonAbstract
{

    public $code;

    public function init()
    {
        parent::init();
        $this->code = \Yii::$app->request->post('code', '');

    }

    public function login()
    {
        try {

            $userModel->showField = 'user_id,unionid, username, nickname,wx_bind, avatar_head, user_sex, user_extend, gps_lng, gps_lat, user_state';
        $userData = $userModel->info(['apple_user_id' => $apple_code]);
    }

    public function logout()
    {
        // TODO: Implement logout() method.
    }

}