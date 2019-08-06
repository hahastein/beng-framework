<?php


namespace bengbeng\framework\user;

use yii\base\UnknownPropertyException;

/**
 * Class User
 * @package bengbeng\framework\user
 * @property AddressLogic $address
 * @property AccountLogic $account
 * @property FriendLogic $friend
 * @property GroupLogic $group
 */
class User
{

    public $util;

    private $userID;
    private $unionID;

    private $components;

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
     * @param $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        if(isset($this->components[$name])){
            return $this->components[$name];
        }else{
            $getter = 'get' . $name;
            if (method_exists($this, $getter)) {
                // read property, e.g. getName()
                return $this->components[$name] = $this->$getter();
            }
            throw new UnknownPropertyException('没有找到此功能: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @return AccountLogic
     */
    public function getAccount()
    {
        $account = new AccountLogic();
        $account->setUserID($this->userID);
        $account->setUnionID($this->unionID);
        return $account;
    }

    /**
     * @return AddressLogic
     */
    public function getAddress()
    {
        $address = new AddressLogic();
        $address->setUserID($this->userID);
        $address->setUnionID($this->unionID);
        return $address;
    }

    /**
     * @return FriendLogic
     */
    public function getFriend()
    {
        $friend = new FriendLogic();
        $friend->setUserID($this->userID);
        $friend->setUnionID($this->unionID);
        return $friend;
    }

    /**
     * @return GroupLogic
     */
    public function getGroup()
    {
        $group = new GroupLogic();
        $group->setUserID($this->userID);
        $group->setUnionID($this->unionID);
        return $group;
    }
}