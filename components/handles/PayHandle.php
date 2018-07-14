<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/14
 * Time: 2:51
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\plugins\alipay\AlipayTradeAppPayRequest;
use bengbeng\framework\components\plugins\alipay\AopClient;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;

class PayHandle
{
    private $payType;
    private $payBody;
    private $paySubject;
    private $payOrderSn;
    private $payAmount;
    private $payNotifyUrl;
    private $error;

    public function __construct()
    {
        $this->payType = Enum::PAY_TYPE_WXPAY;
    }

    public function configForApp(){
        try{
            self::checkParams();
            if($this->payType == Enum::PAY_TYPE_WXPAY){
                return self::configAppByWeixin();
            }else if($this->payType == Enum::PAY_TYPE_ALIPAY){
                return self::configAppByAliPay();
            }else{
                throw new \Exception('没有此支付类型');
            }
        }catch (InvalidConfigException $ex){
            $this->error = $ex->getMessage();
            return false;
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * @throws \Exception
     */
    private function checkParams(){
        if(empty($this->payBody)){
            throw new \Exception('支付内容不能为空');
        }

        if(empty($this->paySubject) && $this->payType == Enum::PAY_TYPE_ALIPAY){
            throw new \Exception('支付标题不能为空');
        }

        if(empty($this->payAmount) && $this->payAmount > 0){
            throw new \Exception('支付金额不能小于1分钱');
        }

        if(empty($this->payOrderSn) || strlen($this->payOrderSn) < 5){
            throw new \Exception('支付订单格式不正确');
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function configAppByAliPay(){
        try {

            $aop = new AopClient();

            $alipayConfig = \Yii::$app->params['Alipay'];
            if (!isset($alipayConfig) || !is_array($alipayConfig)) {
                throw new \Exception('配置项错误');
            }
            $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
            $aop->appId = $alipayConfig['app_id'];
            $aop->rsaPrivateKey = $alipayConfig['rsaPrivateKey'];
            $aop->alipayrsaPublicKey = $alipayConfig['rsaPublicKey'];
            $aop->format = "json";
            $aop->signType = "RSA2";

            $request = new AlipayTradeAppPayRequest();

            $bizContent = json_encode([
                'body' => $this->payBody,
                'subject' => $this->paySubject,
                'out_trade_no' => $this->payOrderSn,
                'timeout_express' => '30m',
                'total_amount' => $this->payAmount,
                'product_code' => 'QUICK_MSECURITY_PAY'
            ]);
            $request->setNotifyUrl($this->payNotifyUrl);
            $request->setBizContent($bizContent);
            $returnData = $aop->sdkExecute($request);
            return [
                'aliSign' => $returnData
            ];
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function configAppByWeixin(){
        $app = Factory::payment(\Yii::$app->params['WECHAT']);
        $result = $app->order->unify([
            'body' => $this->payBody,
            'out_trade_no' => $this->payOrderSn,
            'total_fee' => $this->payAmount*100,
            'notify_url' => $this->payNotifyUrl, // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'APP',
        ]);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            unset($result['return_code']);
            unset($result['result_code']);
            $result['package'] = 'Sign=WXPay';
            $result['timeStamp'] = time();
        }else{
            throw new InvalidConfigException($result['return_msg']);
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
     * @param mixed $payBody
     */
    public function setPayBody($payBody)
    {
        $this->payBody = $payBody;
    }

    /**
     * @param mixed $paySubject
     */
    public function setPaySubject($paySubject)
    {
        $this->paySubject = $paySubject;
    }

    /**
     * @param mixed $payOrderSn
     */
    public function setPayOrderSn($payOrderSn)
    {
        $this->payOrderSn = $payOrderSn;
    }

    /**
     * @param mixed $payAmount
     */
    public function setPayAmount($payAmount)
    {
        $this->payAmount = $payAmount;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


}