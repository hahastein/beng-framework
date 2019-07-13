<?php


namespace bengbeng\framework\components\driver\sms;


use bengbeng\framework\components\helpers\NullHelper;

abstract class SmsDriverAbstract
{

    /**
     * @var boolean 请求地址环境设置，true为正式环境 false为开发环境
     */
    public $environment;
    /**
     * @var string|array 发送的内容
     */
    public $sendContent;

    public $appName;

    /**
     * @var string 返回的提示语
     */
    public $message;

    /**
     * SmsDriverAbstract constructor.
     * @param array $config 配置文件
     */
    public function __construct($config)
    {
        $this->sendContent = NullHelper::arrayKey($config, 'content');
        $this->appName = NullHelper::arrayKey($config, 'appName');
        $this->environment = NullHelper::arrayKey($config, 'environment');
    }

    /**
     * 发送验证码
     * 如果获取错误信息，获取message属性
     * @param int $phone 发送验证码的手机号
     * @param string $code 发送的验证码
     * @param int $templateID 设置模板ID 0为自定义发送内容
     * @return bool
     */
    abstract function singleSend($phone, $code, $templateID = 0);


    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}