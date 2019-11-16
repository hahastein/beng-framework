<?php


namespace bengbeng\framework\user;

use bengbeng\framework\base\Bootstrap;

/**
 * 用户系统入口
 * Class User
 * @package bengbeng\framework\user
 * @property AddressLogic $address
 * @property AccountLogic $account
 * @property FriendLogic $friend
 * @property GroupLogic $group
 * @property OrderLogic $order
 * @property WalletLogic $wallet
 * @property FavoritesLogic $favorites
 */
class User extends Bootstrap
{

    public function init()
    {
        parent::init();
        $this->moduleName = 'user';
    }
}