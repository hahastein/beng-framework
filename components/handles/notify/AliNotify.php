<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-08-22
 * Time: 17:05
 */

namespace bengbeng\framework\components\handles\notify;


use bengbeng\framework\components\plugins\alipay\AopClient;
use yii\helpers\ArrayHelper;

class AliNotify
{
    private $config;
    private $aopClient;

    public function __construct(array $config)
    {
        $this->config = \Yii::$app->params['Alipay'];
        $this->aopClient = new AopClient();

        $this->aopClient->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $this->aopClient->appId = $this->config['app_id'];
        $this->aopClient->rsaPrivateKey = $this->config['rsaPrivateKey'];
        $this->aopClient->alipayrsaPublicKey = $this->config['rsaPublicKey'];
        $this->aopClient->format = "json";
        if(isset($alipayConfig['signType'])) {
            $this->aopClient->signType = $this->config['signType'];
        }else{
            $this->aopClient->signType = "RSA";
        }
    }

    public function getNotifyData()
    {
        $postData = \Yii::$app->request->post();
        $data = empty($postData) ? \Yii::$app->request->get() : $postData;
        if (empty($data) || ! is_array($data)) {
            return false;
        }
        return $data;
    }

    /**
     * 支付宝返回 成功 success 失败 fail
     * @param boolean $flag 返回类型 true成功，false失败
     * @param string $msg 错误原因
     * @return string
     */
    public function replyNotify($flag, $msg = '')
    {
        if ($flag) {
            return 'success';
        } else {
            return 'fail';
        }
    }

    public function verifySign(array $data){
        $signType = strtoupper($data['sign_type']);
//        $sign = $data['sign'];

//        1. 剔除sign与sign_type参数
//        ArrayHelper::remove($data, 'sign');
//        ArrayHelper::remove($data, 'sign_type');

        return $this->aopClient->rsaCheckV1($data, '', $signType);
    }
}