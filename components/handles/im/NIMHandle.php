<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-17
 * Time: 17:15
 */

namespace bengbeng\framework\components\handles\im;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\im\Yunxin\NIMFriend;
use bengbeng\framework\components\handles\im\Yunxin\NIMUser;

class NIMHandle
{
    /**
     * @var NIMUser $user
     */
    public $user;

    /**
     * @var NIMFriend $friend
     */
    public $friend;

    /**
     * 参数初始化
     * NIMHandle constructor.
     * @param int $postType
     */
    public function __construct($postType = Enum::IM_REQUEST_POST_TYPE_CURL){

        $this->user = new NIMUser($postType);
        $this->friend = new NIMFriend($postType);

    }

}