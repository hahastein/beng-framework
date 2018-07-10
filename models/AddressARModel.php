<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 17:59
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class AddressARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%address}}';
    }
}