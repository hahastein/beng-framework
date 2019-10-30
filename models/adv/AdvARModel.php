<?php


namespace bengbeng\framework\models\adv;


use bengbeng\framework\base\BaseActiveRecord;

class AdvARModel extends BaseActiveRecord
{

    public static function tableName()
    {
        return '{{%adv}}';
    }

}