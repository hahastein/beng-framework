<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\CacheHandle;
use bengbeng\framework\models\UserARModel;

class UserBase
{

    private $userID;
    private $unionID;

    /**
     * @var UserProperty $user
     */
    private $user;

    protected $model;
    protected $saveParams;

    protected $error;

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return UserProperty
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserProperty $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $unionID
     */
    public function setUnionID($unionID)
    {
        $this->unionID = $unionID;

        if(!$this->userID){
            //如果没有userid，需要将unionid转换为userid
            $this->user = $this->unionToUser();
            $this->userID = $this->user->userID;
        }
    }

    private function unionToUser(){

        $userProperty = UserUtil::getCache($this->unionID);

        if($userProperty && isset($userProperty->userID)){
            return $userProperty;
        }

        return false;

    }
}