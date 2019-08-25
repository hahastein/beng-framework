<?php


namespace bengbeng\framework\models\admin;


use bengbeng\framework\base\BaseActiveRecord;

class AdminPermissionsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%admin_permissions}}';
    }
}