<?php


namespace bengbeng\framework\models\admin;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class AdminPermissionsARModel
 * @property integer $admin_id
 * @property integer $permissions_id
 * @property integer $gp_id
 * @package bengbeng\framework\models\admin
 */
class AdminPermissionsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%admin_permissions}}';
    }
}