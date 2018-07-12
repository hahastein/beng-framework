<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 22:16
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class EvaluateARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%evaluate}}';
    }
}