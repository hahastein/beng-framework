<?php


namespace bengbeng\framework\components\driver\sms;


use bengbeng\framework\components\helpers\NullHelper;

abstract class SmsDriverAbstract
{

    const SMS_SEND_CONTENT_CODE = '【#app#】您的验证码是#code#';

    /**
     * @var boolean 请求地址环境设置，true为正式环境 false为开发环境
     */
    public $environment;
    /**
     * @var string|array 发送的内容
     */
    public $sendContent;

    /**
     * @var string 返回的提示语
     */
    public $message;

    /**
     * SmsDriverAbstract constructor.
     * @param array $config 配置文件
     * @param string|bool $sendType
     */
    public function __construct($config, $sendType = false)
    {
        if(array_key_exists('content', $config) && !empty($config['content'])){
            $this->sendContent = $config['content'];
        }else{
            $this->sendContent = $sendType;
        }

        $this->environment = NullHelper::arrayKey($config, 'environment');

    }

    /**
     * 发送验证码
     * 如果获取错误信息，获取message属性
     * @param int $phone 发送验证码的手机号
     * @param int $templateID 设置模板ID 0为自定义发送内容
     * @return bool
     */
    abstract function singleSend($phone, $templateID = 0);

}