<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/14
 * Time: 2:51
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;

class PayHandle
{
    private $payType;

    private $error;

    public function __construct()
    {
        $this->payType = Enum::PAY_TYPE_WXPAY;
    }

    public function configForApp(){
        $app = Factory::payment(\Yii::$app->params['WECHAT']);

        try{
            $result = $app->order->unify([
                'body' => '测试',
                'out_trade_no' => '20180022311001',
                'total_fee' => 1,
                'notify_url' => '/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
                'trade_type' => 'APP',
            ]);
        }catch (InvalidConfigException $ex){
            $this->error = $ex->getMessage();
            return false;
        }


        return $result;
    }

    public function send(){

    }

    /**
     * @param int $payType
     */
    public function setPayType($payType)
    {
        $this->payType = $payType;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
}