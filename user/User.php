<?php


namespace bengbeng\framework\user;

use yii\base\UnknownPropertyException;

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
     * @param $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {

        $getter = 'get' . $name;

        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);

    }
    /**
     * @return AddressLogic
     */
    public function getAddress()
    {
var_dump($this->address);
        $address = new AddressLogic();
        $address->setUserID($this->userID);
        $address->setUnionID($this->unionID);

        return $address;
    }

}