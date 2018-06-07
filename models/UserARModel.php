<?php
namespace bengbeng\framework;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

class UserARModel extends ActiveRecord{

    public static function tableName(){
        return '{{%user}}';
    }

    public function info($where = []){
        return self::find()->where($where)->one();
    }

}