<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-16
 * Time: 11:52
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * Class FollowARModel
 * @property integer $follow_id
 * @property integer $user_id
 * @property integer $obj_id
 * @property integer $status
 * @property integer $addtime
 * @package bengbeng\framework\models
 */
class FollowARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%follow}}';
    }

    public function findByUserRelation($user_id, $obj_id){
        return self::find()->where([
            'user_id' => $user_id,
            'obj_id' => $obj_id
        ])->one();
    }
}