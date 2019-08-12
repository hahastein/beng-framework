<?php


namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * 用户身份证表
 * Class UserIDARModel
 * @property integer $ID_id
 * @property integer $user_id
 * @property string $id_no
 * @property string $id_name
 * @property string $id_front_img
 * @property string $id_back_img
 * @package bengbeng\framework\models
 */
class UserIDARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%user_ID}}';
    }

    /**
     * @param $user_id
     * @return static|\yii\db\ActiveRecord|null
     */
    public function findByUserID($user_id){
        return self::info([
            'user_id' => $user_id
        ]);
    }
}