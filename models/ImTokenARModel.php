<?php


namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;

class ImTokenARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%user_token}}';
    }
}