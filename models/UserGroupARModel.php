<?php


namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class UserGroupARModel
 * @property integer $group_id
 * @property integer $im_group_id
 * @property string $group_name
 * @property integer $create_user_id
 * @property string $group_desc
 * @property integer $upper_limit
 * @property integer $user_sum
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 * @property integer $mode  10系统20用户定义
 * @package bengbeng\framework\models
 */
class UserGroupARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%im_group}}'; // TODO: Change the autogenerated stub
    }


    /**
     * @param $groupID
     * @param $userID
     * @return UserGroupARModel|null
     */
    public function findInfoByGroupID($groupID, $userID){
        return self::info([
            'group_id' => $groupID,
            'create_user_id' => $userID
        ]);
    }

    public function findInfoByName($name){
        return self::info([
            'group_name' => $name
        ]);
    }
}