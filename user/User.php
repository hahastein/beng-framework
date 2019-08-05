<?php


namespace bengbeng\framework\user;


class User
{

    public $util;
    /**
     * @var AccountLogic $account
     */
    public $account;

    /**
     * @var AddressLogic $address
     */
    public $address;

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
     * @param mixed $unionID
     */
    public function setUnionID($unionID)
    {
        $this->unionID = $unionID;
    }

    /**
     * @return AccountLogic
     */
    public function getAccount()
    {
        return $this->account?$this->account:new AccountLogic();
    }

    /**
     * @return AddressLogic
     */
    public function getAddress()
    {
        if(!$this->address){
            $this->address = new AddressLogic();
        }
        $this->address->userID = $this->userID;
        $this->address->unionID = $this->unionID;
        return $this->address;
    }
}