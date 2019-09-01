<?php


namespace bengbeng\framework\models\order;


use bengbeng\framework\base\BaseActiveRecord;

class OrderGoodsARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_order_goods}}';
    }
}