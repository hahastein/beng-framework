<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

class UserTokenARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    public function findByImID($imid){
        return self::dataOne(function (ActiveQuery $query) use($imid){
            $query->where(['unionid' => $imid]);
        });
    }
}