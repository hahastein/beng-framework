<?php


namespace bengbeng\framework\models\admin;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class PermissionsGroupARModel
 * @property integer $pg_id
 * @property string $pg_name
 * @property array $permissions_ids
 * @package bengbeng\framework\models\admin
 */
class PermissionsGroupARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%permissions_group}}';
    }
}