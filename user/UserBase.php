<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\CacheHandle;
use bengbeng\framework\models\UserARModel;

class UserBase
{

    private $userID;
    private $unionID;

    public function __construct()
    {
    }


    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }
    /**
     * @param string $unionID
     */
    public function setUnionID($unionID)
    {
        if(!$this->userID){
            //如果没有userid，需要将unionid转换为userid
            $this->userID = $this->unionToUser();
        }
        $this->unionID = $unionID;
    }

    private function unionToUser(){

        $userData = CacheHandle::get(Enum::CACHE_USER_DATA, function (){
            var_dump($this->unionID);die;
            return (new UserARModel())->findAllByUnionId($this->unionID);
        });

        if(array_key_exists('user_id', $userData)){
            return $userData['user_id'];
        }

        return false;

    }
}