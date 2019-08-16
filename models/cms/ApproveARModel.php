<?php


namespace bengbeng\framework\models\cms;

use bengbeng\framework\base\BaseActiveRecord;

class ApproveARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_approve}}';
    }
}