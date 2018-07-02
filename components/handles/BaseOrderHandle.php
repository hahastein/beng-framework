<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-02
 * Time: 15:19
 */
namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\OrderARModel;
use yii\db\Exception;

/**
 * Class BaseOrderHandle
 * @property int $user_id 用户ID
 * @property array $order_fields 保存的参数
 * @package bengbeng\framework\components\handles
 */
class BaseOrderHandle
{

    // 支付类型0线下1支付宝2微信3银联4苹果支付
    const PAY_TYPE_NOPAY = -1;
    const PAY_TYPE_OFFLINE = 0;
    const PAY_TYPE_ALIPAY = 1;
    const PAY_TYPE_WXPAY = 2;
    const PAY_TYPE_UNIONPAY = 3;
    const PAY_TYPE_APPLEPAY = 4;

    //订单状态0未支付1已支付2退款中3退款完成4取消订单5异常订单
    const STATUS_NOPAY = 0;
    const STATUS_PAY_FINISH = 1;
    const STATUS_REFUND = 2;
    const STATUS_REFUND_FINISH = 3;
    const STATUS_CANCEL = 4;
    const STATUS_EXCEPTION = 5;


    public $order_fields;
    public $user_id;
    public $store_id;

    private $orderSn;
    private $paySn;

    private $orderModel;

    public function __construct(){
        $this->orderModel = new OrderARModel();
    }

    /**
     * 创建基础订单表数据，回调处理自己的数据
     * @throws Exception
     */
    public function create(){

        try{
            $this->orderSn = self::makeOrderSn($this->user_id, $this->store_id);
            $this->paySn = self::makePaySn($this->user_id, $this->store_id);

            $this->order_fields['order_sn'] = $this->orderSn;
            $this->order_fields['order_pay_sn'] = $this->paySn;
            $this->order_fields['user_id'] = $this->getUserId();
            $this->order_fields['addtime'] = time();
            $this->order_fields['pay_type'] = self::PAY_TYPE_NOPAY;
            $this->order_fields['order_status'] = self::STATUS_NOPAY;

            $this->orderModel->setAttributes($this->order_fields, false);
            if($this->orderModel->save()){
                return true;
            }else{
                throw new Exception('创建订单基础数据失败');
            }
        }catch (Exception $ex){
            throw  $ex;
        }
    }

    /**
     * @param int $userID
     * @param int $storeID
     * @return string
     */
    public function makeOrderSn($userID, $storeID = 0){
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        $pay_id_1 = mt_rand(1000, 9999);
        $pay_id_2 = mt_rand(1000, 9999);
        $pay_id = $pay_id_1.$pay_id_2;
        $pay_id = (int)$pay_id + $userID;
        if($storeID>0){
            $pay_id = $pay_id + $storeID;
        }
        $pay_id = $pay_id + time();

        return (date('y', time()) % 9 + 1) . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }

    public function makePaySn($userID, $storeID = 0){
        return mt_rand(10, 99)
            . sprintf('%010d', time() - 946656000)
            . sprintf('%03d', (float)microtime() * 1000)
            . sprintf('%03d', (int)$userID % 1000);
    }

    /**
     * @return mixed
     */
    public function getOrderFields()
    {
        return is_array($this->order_fields)?$this->order_fields:[];
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return isset($this->store_id)?$this->store_id:0;
    }

    /**
     * @param mixed $order_fields
     */
    public function setOrderFields($order_fields)
    {
        $this->order_fields = $order_fields;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @param mixed $store_id
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
    }
}