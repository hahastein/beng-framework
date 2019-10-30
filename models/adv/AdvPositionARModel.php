<?php


namespace bengbeng\framework\models\adv;


use bengbeng\framework\base\BaseActiveRecord;

class AdvPositionARModel extends BaseActiveRecord
{

    public static function tableName()
    {
        return '{{%adv_position}}';
    }

    public function getAdv(){
        return $this->hasMany(AdvARModel::className(),['ap_id'=>'ap_id'])->select(['ap_id', 'adv_pic', 'link_url']);
    }

}