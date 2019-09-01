<?php


namespace bengbeng\framework\models\order;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class OrderCommonARModel
 * @property integer $order_id
 * @property integer $store_id 店铺ID
 * @property integer $shipping_time 配送时间
 * @property integer $shipping_express_id 配送公司ID
 * @property string $order_message 订单留言
 * @property float $voucher_price 代金券面额
 * @property string $voucher_code 代金券编码
 * @property float $order_pointscount 订单赠送积分
 * @property string $deliver_explain 发货备注
 * @property integer $daddress_id 发货地址ID
 * @property string $reciver_name 收货人姓名
 * @property array $reciver_info 收货人其它信息
 * @property integer $reciver_province_id 收货人省级ID
 * @property integer $reciver_city_id 收货人市级ID
 * @property integer $promotion_total 订单总优惠金额(代金券,满减,平台红包)
 * @property integer $discount 会员折扣x%
 * @package bengbeng\framework\models\order
 */
class OrderCommonARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_order_common}}';
    }
}