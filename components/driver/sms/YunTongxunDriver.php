<?php
namespace bengbeng\framework\components\driver\sms;

use bengbeng\framework\components\helpers\NullHelper;
use bengbeng\framework\components\driver\sms\sdk\YunTongxunSDK;

/**
 * Class YunTongxunDriver
 * SDK URL: http://www.yuntongxun.com
 * @package bengbeng\framework\components\driver\sms
 */
class YunTongxunDriver extends SmsDriverAbstract
{
    const SMS_SEND_DEFAULT_CONTENT = ['#code#'];

    /**
     * @var string 应用ID
     */
    private $appId = '';
    /**
     * @var string 主账号，对发者主账号下的 Account SID
     */
    private $accountSid = '';

    /**
     * @var string 主账号令牌，开发者主账号下的 Account Token
     */
    private $accountToken = '';

    private $sdk;

    public function __construct($config)
    {
        parent::__construct($config);

        if(!$this->sendContent){
            $this->sendContent = self::SMS_SEND_DEFAULT_CONTENT;
        }

        if(NullHelper::arrayKey($config, 'sdk')){
            $this->appId = NullHelper::arrayKey($config['sdk'], 'appId');
            $this->accountSid = NullHelper::arrayKey($config['sdk'], 'accountSid');
            $this->accountToken = NullHelper::arrayKey($config['sdk'], 'accountToken');
        }

        if(!$serverIP = NullHelper::arrayKey($config['sdk'], 'ip')){
            $serverIP = $this->setEnvRequest();
        }

        if(!$serverPort = NullHelper::arrayKey($config['sdk'], 'port')){
            $serverPort = '8883';
        }

        if(!$softVersion = NullHelper::arrayKey($config['sdk'], 'version')){
            $softVersion = '2013-12-26';
        }

        $this->sdk = new YunTongxunSDK($serverIP, $serverPort, $softVersion);
        $this->sdk->setAccount($this->accountSid, $this->accountToken);
        $this->sdk->setAppId($this->appId);

    }

    public function singleSend($phone, $code, $templateID = '0')
    {
        foreach ($this->sendContent as $key => $item){
            if($item == '#code#'){
                $this->sendContent[$key] = str_replace('#code#', $code, $item);
                break;
            }
        }

        $result = $this->sdk->sendTemplateSMS($phone, $this->sendContent, $templateID);
        if($result == NULL){
            $this->message = 'SDK错误，可能是配置出错';
            return false;
        }
        if($result->statusCode != 0){
            $this->message = $result->statusMsg;
            return false;
        }else{
            $this->message = '发送成功';
            return true;
        }
    }

    private function setEnvRequest(){
        if($this->environment){
            return 'app.cloopen.com';
        }else{
            return 'sandboxapp.cloopen.com';
        }
    }
}