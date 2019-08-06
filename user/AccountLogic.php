<?php


namespace bengbeng\framework\user;

use yii\db\Exception;

class AccountLogic extends UserBase
{

    public function addFriend($friendUnionID){

        try{
            $myID = $this->getUserID();
            $friendID = UserUtil::getCache($friendUnionID);
        }catch (Exception $ex){

        }
    }
}