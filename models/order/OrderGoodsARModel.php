<?php


namespace bengbeng\framework\models\order;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class OrderGoodsARModel
 * @property integer $rec_id
 * @property integer $order_id
 * @property integer $goods_id 商品ID
 * @property string $goods_name 商品名称
 * @property float $goods_price 商品价格
 * @property integer $goods_num 商品数量
 * @property string $goods_image 商品原图片
 * @property integer $store_id 店铺ID
 * @property integer $buyer_id 买家ID
 * @property integer $goods_type 1默认2团购商品3限时折扣商品4组合套装5赠品8加价购活动商品9加价购换购商品
 * @property integer $createtime 创建时间
 * @package bengbeng\framework\models\order
 */
class OrderGoodsARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_order_goods}}';
    }
}