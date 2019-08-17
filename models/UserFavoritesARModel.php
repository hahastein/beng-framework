<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;

/**
 * Class UserFavoritesARModel
 * @property integer $fav_id
 * @property integer $object_id
 * @property integer $user_id
 * @property integer $module
 * @property integer $createtime
 * @package bengbeng\framework\models
 */
class UserFavoritesARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%user_favorites}}';
    }

    public function exists($object_id, $user_id, $module = Enum::MODULE_TYPE_ARTICLE){
        return self::find()->where([
            'object_id ' => $object_id,
            'user_id' => $user_id,
            'module' => $module
        ])->exists();
    }
}