<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-02
 * Time: 17:36
 */

namespace bengbeng\framework\models\order;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class OrderARModel
 * @property integer $order_id
 * @property integer $comment_id 公共ID
 * @property integer $group_id 分组ID
 * @property integer $order_sn 订单编号
 * @property integer $pay_sn 支付单号
 * @property integer $store_id 店铺id
 * @property string $store_name 店铺名称
 * @property integer $buyer_id 买家ID
 * @property string $buyer_name 买家名称
 * @property integer $buyer_phone 买家电话
 * @property integer $add_time 订单生成时间
 * @property integer $payment_code 支付方式0未支付10微信11支付宝12网银20积分支付30余额支付
 * @property integer $payment_time 支付(付款)时间
 * @property integer $finished_time 订单完成时间
 * @property float $goods_amount 商品总价格
 * @property float $order_amount 订单总价格
 * @property float $goods_integral 商品总积分
 * @property float $order_integral 订单总积分
 * @property float $shipping_fee 运费
 * @property integer $order_state 订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;
 * @property integer $refund_state 退款状态:0是无退款,1是部分退款,2是全部退款
 * @property integer $lock_state 锁定状态:0是正常,大于0是锁定,默认是0
 * @property integer $delete_state 删除状态0未删除1放入回收站2彻底删除
 * @property float $refund_amount 退款金额
 * @property string $shipping_code 物流单号
 * @property integer $order_type 订单类型1普通订单(默认),2预定订单,3门店自提订单10拼单订单
 * @property integer $api_pay_time 在线支付动作时间,只要向第三方支付平台提交就会更新
 * @property string $trade_no 外部交易号
 * @package bengbeng\framework\models
 */
class OrdersARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_orders}}';
    }

}