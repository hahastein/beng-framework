<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\models\UserARModel;

/**
 * Class FaqIdentifyARModel
 * @property integer $question_id
 * @property integer $user_id
 * @property string $unionid
 * @package bengbeng\framework\models\cms
 */
class FaqIdentifyARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_faq_identify}}';
    }

    public function getUser(){
        return $this->hasOne(UserARModel::className(),['user_id'=>'user_id'])->select([
            'nickname',
            'avatar_head',
            'user_id',
            'auth_info'
        ]);
    }

    public function getQuestion(){
        return $this->hasOne(UserARModel::className(),['question_id'=>'question_id']);
    }

}