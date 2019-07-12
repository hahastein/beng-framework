<?php


namespace bengbeng\framework\components\driver\sms;

use bengbeng\framework\components\helpers\NullHelper;
use yii\db\Exception;
use Yunpian\Sdk\YunpianClient;
use Yunpian\Sdk\YunpianConf;

class YunPianDriver extends SmsDriverAbstract
{

    private $https = false;
    private $yunPianKey = '';
    private $yunPianClient;

    public function __construct($config, $sendType = false)
    {
        parent::__construct($config, $sendType);
        $this->https = NullHelper::arrayKey($config, 'https');
        $this->yunPianKey = NullHelper::arrayKey($config, 'key');

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

    public function singleSend($phone, $templateID = 0)
    {
        try{
            if($phone){
                throw new \Exception('手机号不能为空');
            }

            if($this->sendContent){
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