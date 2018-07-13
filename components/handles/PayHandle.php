<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/14
 * Time: 2:51
 */

namespace bengbeng\framework\components\handles;

use EasyWeChat\Factory;

class PayHandle
{
    public function __construct()
    {
    }

    public function configForApp(){
        $app = Factory::payment(\Yii::$app->params['WECHAT']);

        $result = $app->order->unify([
            'body' => '测试',
            'out_trade_no' => '20180022311001',
            'total_fee' => 1,
            'notify_url' => '/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'APP',
        ]);

        return $result;
    }

    public function send(){

    }
}