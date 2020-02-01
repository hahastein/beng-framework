<?php


namespace bengbeng\framework\user;

/**
 * Class UserProperty
 * @package bengbeng\framework\user
 */
class UserProperty
{
    public $userID;
    public $userName;
    public $unionID;
    public $phone;
    public $phoneBind;
    public $wxOpenid;
    public $wxBind;
    public $nickname;
    public $userSex;
    public $avatarHead;
    public $intro;
    public $isAuth;
    public $driverUuid;
    public $userState;
    public $imToken;
    public $imID;
    public $tags;

    public function __construct($cacheData)
    {
        $this->userID = $cacheData['user_id'];
        $this->unionID = $cacheData['unionid'];
        $this->userName = $cacheData['username'];
        $this->nickname = $cacheData['nickname'];
        $this->userSex = $cacheData['user_sex'];
        $this->userState = $cacheData['user_state'];
        $this->phone = $cacheData['phone_num'];
        $this->phoneBind = $cacheData['phone_bind'];
        $this->wxBind = $cacheData['wx_bind'];
        $this->wxOpenid = $cacheData['wx_openid'];
        $this->avatarHead = $cacheData['avatar_head'];
        $this->isAuth = $cacheData['is_auth'];
        $this->intro = $cacheData['intro'];

        if(isset($cacheData['imToken'])){
            $this->imID = $cacheData['imToken']['unionid'];
            $this->imToken = $cacheData['imToken']['im_token'];
        }

        if(isset($cacheData['user_extend'])){
            if(!is_array($cacheData['user_extend'])){
                $cacheData['user_extend'] = json_decode($cacheData['user_extend'], true);
            }
            $this->tags = $cacheData['user_extend']['tags'];
        }
    }
}