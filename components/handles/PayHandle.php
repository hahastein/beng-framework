<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/14
 * Time: 2:51
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\base\Enum;
use bengbeng\framework\components\plugins\alipay\AlipayTradeAppPayRequest;
use bengbeng\framework\components\plugins\alipay\AopCertClient;
use bengbeng\framework\components\plugins\alipay\AopClient;
use bengbeng\framework\models\order\OrdersARModel;
//use bengbeng\framework\models\OrderARModel;
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

    private $isDebug;
    private $debugData;

    public function __construct()
    {
        $this->payType = Enum::PAY_TYPE_WXPAY;
        $this->isDebug = false;
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


            $alipayConfig = \Yii::$app->params['Alipay'];
            if (!isset($alipayConfig) || !is_array($alipayConfig)) {
                throw new \Exception('配置项错误');
            }

            //按配置生成
            if($alipayConfig['mode'] == 'cert'){
                $aop = new AopCertClient();
                $aop->alipayrsaPublicKey = $aop->getPublicKey($alipayConfig['cert']['alipayPath']);//调用getPublicKey从支付宝公钥证书中提取公钥
                $aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
                $aop->appCertSN = $aop->getCertSN($alipayConfig['cert']['appPath']);//调用getCertSN获取证书序列号
                $aop->alipayRootCertSN = $aop->getRootCertSN($alipayConfig['cert']['rootPath']);//调用getRootCertSN获取支付宝根证书序列号
            }else{
                $aop = new AopClient();
                $aop->alipayrsaPublicKey = $alipayConfig['rsaPublicKey'];
            }


            $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
            $aop->appId = $alipayConfig['app_id'];
            $aop->rsaPrivateKey = $alipayConfig['rsaPrivateKey'];
            $aop->format = "json";
            if(isset($alipayConfig['signType'])) {
                $aop->signType = $alipayConfig['signType'];
            }else{
                $aop->signType = "RSA";
            }

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
            $configApp = $app->jssdk->appConfig($result['prepay_id']);
            return $configApp;
        }else{
            if($result['result_code'] == 'FAIL') {
                throw new InvalidConfigException($result['err_code_des']);
            }else{
                throw new InvalidConfigException('未知错误');
            }
        }
    }

    public function send(){

    }

    /**
     * @param \Closure $closure
     */
    public function notify(\Closure $closure){
        if($this->payType == Enum::PAY_TYPE_WXPAY){
            try{
                $app = Factory::payment(\Yii::$app->params['WECHAT']);
                $response = $app->handlePaidNotify(function($message, $fail) use ($closure){
                    if($this->isDebug && !empty($this->debugData)){
                        $message = $this->debugData;
                    }
                    $out_trade_no = $message['out_trade_no']; //订单号
                    $transaction_id = $message['transaction_id']; //外部交易号
                    $total_fee = $message['total_fee'] / 100; //支付金额
                    if ($message['return_code'] === 'SUCCESS') { //return_code 表示通信状态，不代表支付状态
                        if ($message['result_code'] === 'SUCCESS') {
                            //支付成功
                            call_user_func($closure, $out_trade_no, $total_fee, $transaction_id);
                        } elseif ($message['result_code'] === 'FAIL') {
                            //支付失败
                            $orderModel = new OrdersARModel();
                            $orderModel->dataUpdate(function (ActiveOperate $operate) use($out_trade_no, $transaction_id){
                                $operate->where([
                                    'order_sn' => $out_trade_no
                                ]);
                                $operate->params([
                                    'order_state' => Enum::ORDER_STATUS_EXCEPTION,
                                    'transaction_id' => $transaction_id
                                ]);
                            });
                            //是否写入日志
                        }
                    } else {
                        return $fail('通信失败，请稍后再通知我');
                    }
                    return true; //处理完成，不再请求我
                });
                $response->send();
            }catch (\EasyWeChat\Kernel\Exceptions\Exception $e){
//                file_put_contents('/www/yqlh_log/wx_error'.date('Y-m-d', time()).'.log',$e->getMessage(),FILE_APPEND);
            }
        }else if($this->payType == Enum::PAY_TYPE_ALIPAY){
            $aliPayData = \Yii::$app->request->post();
            try{
                if ($aliPayData['trade_status'] == 'TRADE_SUCCESS' && $aliPayData['notify_type'] == 'trade_status_sync') {

                    echo 'SUCCESS';
                }else{
                    echo 'FAIL';
                }
            }catch (\Exception $ex){

            }
        }
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
     * @param mixed $payNotifyUrl
     */
    public function setPayNotifyUrl($payNotifyUrl)
    {
        $this->payNotifyUrl = $payNotifyUrl;
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
     * @param bool $isDebug
     */
    public function setIsDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @param mixed $debugData
     */
    public function setDebugData($debugData)
    {
        $this->debugData = $debugData;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


}