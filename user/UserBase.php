<?php


namespace bengbeng\framework\user;


use bengbeng\framework\models\UserARModel;

class UserBase
{

    private $userID;
    private $unionID;

    /**
     * @var UserProperty $user
     */
    protected $user;

    /**
     * @var UserARModel $userModel
     */
    protected $userModel;

    protected $model;
    protected $saveParams;

    protected $error;

    public function __construct()
    {
        $this->userModel = new UserARModel();
    }

    /**
     * 桥接别的Logic
     * @return User
     */
    protected function bridge(){
        return new User();
    }

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