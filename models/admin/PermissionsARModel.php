<?php


namespace bengbeng\framework\models\admin;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class PermissionsARModel
 * @property integer $permissions_id
 * @property string $name
 * @property integer $parent_id
 * @property string $module
 * @property integer $order
 * @property int $is_set
 * @property integer $createtime
 * @property integer $admin_id
 * @package bengbeng\framework\models\admin
 */
class PermissionsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%permissions}}';
    }

    public function getChild(){
        return $this->hasMany(self::className(),['parent_id'=>'permissions_id']);
    }

    public function findShowLayerByAll(){
        return self::find()->with(['child'])->where(['parent_id' => 0])->orderBy(['order' => SORT_DESC])->asArray()->all();
    }
}