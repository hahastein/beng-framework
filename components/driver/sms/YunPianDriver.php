<?php


namespace bengbeng\framework\components\driver\sms;

use bengbeng\framework\components\helpers\NullHelper;
use yii\db\Exception;
use Yunpian\Sdk\YunpianClient;
use Yunpian\Sdk\YunpianConf;

class YunPianDriver extends SmsDriverAbstract
{

    const SMS_SEND_DEFAULT_CONTENT = '【#app#】您的验证码是#code#';

    private $https = false;
    private $yunPianKey = '';
    private $yunPianClient;
    private $appName;

    public function __construct($config)
    {
        parent::__construct($config);

        if(NullHelper::arrayKey($config, 'sdk')){
            $this->https = NullHelper::arrayKey($config['sdk'], 'https');
            $this->yunPianKey = NullHelper::arrayKey($config['sdk'], 'key');
        }

        if(!$this->sendContent){
            $this->sendContent = self::SMS_SEND_DEFAULT_CONTENT;
        }

        if($this->appName){
            $this->sendContent = str_replace('#app#', $this->appName, $this->sendContent);
        }

        if($this->https){
            $this->yunPianClient = YunpianClient::create($this->yunPianKey);
        }else{
            $this->yunPianClient = YunpianClient::create($this->yunPianKey,[
                'http.conn.timeout' => '10',
                'http.so.timeout' => '30',
                'http.charset' => 'utf-8',
                'yp.version' => 'v2',
                'yp.user.host' => 'http://sms.yunpian.com',
                'yp.sign.host' => 'http://sms.yunpian.com',
                'yp.tpl.host' => 'http://sms.yunpian.com',
                'yp.sms.host' => 'http://sms.yunpian.com',
                'yp.voice.host' => 'http://voice.yunpian.com',
                'yp.flow.host' => 'http://flow.yunpian.com',
                'yp.call.host' => 'http://call.yunpian.com',
                'yp.vsms.host' => 'http://vsms.yunpian.com'
            ]);
        }
    }

    public function singleSend($phone, $code, $templateID = '0')
    {
        try{
            $this->sendContent = str_replace('#code#', $code, $this->sendContent);

            if(!$phone){
                throw new \Exception('手机号不能为空');
            }

            if(!$this->sendContent && $templateID == 0){
                throw new \Exception('没有配置发送内容模板，请在config配置或者设置sendContent属性');
            }

            $yunpian_send = $this->yunPianClient->sms()->single_send([
                YunpianClient::MOBILE => $phone,
                YunpianClient::TEXT => $this->sendContent
            ]);
            if($yunpian_send->isSucc()){
                $this->message = '发送成功';
                return true;
            }else{
                throw new \Exception($yunpian_send->msg());
            }
        }catch (\Exception $ex){
            $this->message = $ex->getMessage();
            return false;
        }
    }
}