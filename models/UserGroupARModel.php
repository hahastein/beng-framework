<?php


namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

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

    public function findInfoByIMID($imID, $userID){
        return self::info([
            'im_group_id' => $imID,
            'create_user_id' => $userID
        ]);
    }

    public function findAllByUserID($userID){
        return self::dataSet(function (ActiveQuery $query) use($userID){

            $query->where([
                'create_user_id' => $userID
            ]);

            $query->asArray();
        });
    }

    public function findAllByMode($mode = 10) {
        return self::dataSet(function (ActiveQuery $query) use($mode){

            $query->where([
                'mode' => $mode
            ]);

            $query->asArray();
        });
    }

    public function findInfoByName($name){
        return self::info([
            'group_name' => $name
        ]);
    }
}