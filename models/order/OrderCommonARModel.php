<?php


namespace bengbeng\framework\models\order;


use bengbeng\framework\base\BaseActiveRecord;

class OrderCommonARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_order_common}}';
    }
}