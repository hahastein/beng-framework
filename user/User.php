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

    public function __get($name)
    {
        var_dump('asdasd - get');die;

        // TODO: Implement __get() method.
    }

    public function __call($name, $arguments)
    {
        var_dump($name.' - call');die;
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

    /**
     * @param AddressLogic $address
     */
    public function setAddress($address)
    {
        var_dump('asdasd - set');die;
        $this->address = $address;
    }
}