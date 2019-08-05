<?php


namespace bengbeng\framework\user;

/**
 * Class User
 * @package bengbeng\framework\user
 * @property AddressLogic $address
 */
class User
{

    public $util;
    /**
     * @var AccountLogic $account
     */
    public $account;

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
        var_dump('asdasd - get');die;
        if(!$this->address){
            $address = new AddressLogic();
        }else{
            $address = $this->address;
        }
        $address->userID = $this->userID;
        $address->unionID = $this->unionID;
        return $address;
    }


    /**
     * @param AddressLogic $address
     */
    public function setAddress($address)
    {
        var_dump('asdasd - set');die;
        $this->address = $address;
    }
}