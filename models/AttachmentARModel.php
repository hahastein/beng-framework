<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 21:01
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class AttachmentARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%attachment}}';
    }

}