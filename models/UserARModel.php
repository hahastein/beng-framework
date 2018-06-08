<?php
namespace bengbeng\framework\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

class UserARModel extends ActiveRecord{

    public static function tableName(){
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            ['phone_num', 'required', 'message' => '填写手机号'],
            ['userpass', 'required', 'when' => function($model){
                return $model->login_type != 10?false:true;
            },'message' => '请您填写密码'],
        ];
    }

    public function info($where = []){
        return self::find()->where($where)->one();
    }
}