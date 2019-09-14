<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class ApplyARModel
 * @property integer $apply_id
 * @property integer $user_id
 * @property integer $apply_type
 * @property string $apply_message
 * @property string $extend_info
 * @property integer $admin_id
 * @property integer $status
 * @property integer $createtime
 * @property integer $updatetime
 * @package bengbeng\framework\models
 */
class ApplyARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%apply}}';
    }

    public function findByAll(){

    }

    public function findByApplyID($apply_id){
        return self::find()->where([
            'apply_id' => $apply_id,
            'status' => 1
        ])->one();
    }

    public function findByTypeAndUserID($user_id, $type){

    }

    public function findOneByTypeAndUserID($user_id, $type){
        return self::find()->where([
            'user_id' => $user_id,
            'apply_type' => $type
        ])->orderBy(['apply_id' => SORT_DESC])->one();
    }

    public function exist($user_id, $type){
        return self::find()->where([
            'user_id' => $user_id,
            'apply_type' => $type
        ])->exists();
    }


}