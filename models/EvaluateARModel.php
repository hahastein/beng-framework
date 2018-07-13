<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 22:16
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * Class EvaluateARModel
 * @property integer $evaluate_id
 * @property string $evaluate_content
 * @property integer $user_id
 * @property integer $star
 * @property integer $obj_id
 * @property integer $evaluate_type
 * @property integer $lookcount
 * @property integer $addtime
 * @package bengbeng\framework\models
 */

class EvaluateARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%evaluate}}';
    }
}