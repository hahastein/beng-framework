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

    public function __construct()
    {
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
        return $this->address?$this->address:new AddressLogic();
    }
}