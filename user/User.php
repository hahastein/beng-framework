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

    public function __get($name)
    {
        var_dump($name.' - get');

        // TODO: Implement __get() method.
    }

    public function __set($name, $value)
    {
        var_dump($name.' - set');
    }

    public function __call($name, $arguments)
    {
        var_dump($name.' - call');
    }


}