#BengBeng Framework - 短信发送使用说明
======================================
* 命名空间
    > use bengbeng\framework\components\handles\SmsHandle;
* 初始化
    ```
        $sms = new SmsHandle(13800138000);
    ```
* 枚举说明
    > SmsHandle::SMS_TYPE_LOGIN 登录验证码
    
    > SmsHandle::SMS_TYPE_REG 注册验证码
    
    > SmsHandle::SMS_TYPE_UNBIND 解绑验证码
    
    > SmsHandle::SMS_TYPE_MORE 其它类型验证码
* 配置文件说明
    ```
        配置文件在params.php的smsConfig节点下
    ```
    * driver (string) 驱动类型名称，目前已有的驱动为云片(YunPian)和云通讯(YunTongxun)
        > driver = 'YunPian'
    * namespace (string) 命名空间，自定义驱动模式时使用，如不是自定义驱动不写即可
        > namespace = '\\common\\handle\\driver\\'
    * appName (string) app的名字，显示在收到验证的名字 一般为[#app#]验证码为：000000
        > appName = '蹦蹦'
    * content (string|array)  发送内容或者第三方模板定义的内容
        > content = '【#app#】#code#(手机验证码，请完成验证)，如非本人操作，请忽略本短信'
        
        > content = ['#app#', 3]
    * sdk (array) 各sdk的配置
        ```
            //云片的设置
            sdk = [
                'key'=>'必传', 
                'https' => false
            ]
        ```
        ```
            //云通讯的设置
            sdk = [
                'appId'=>'必传', 
                'accountSid' => '必传',
                'accountToken' => '必传',
                'ip' => '可不传',
                'port' => '可不传',
                'version' => '可不传'
            ] 
        ```
        ```
            //其它的请安第三方的配置就行设置
        ```
        
* 调用及错误说明
    * 调用发送：(返回 True 和 False)
        > $sms->singleSend($smsType, $templateID); 
        ```
            第一个参数为枚举内的类型
            第二个参数为使用的模板ID，默认为0即使用自定义内容
        ```
    * 调用错误信息：
        > $sms->getMessage;
    * 调用Demo
        ```
        //先在params.php的smsConfig节点下配置好
        
        $sms = new SmsHandle(13800138000);
        
        if($result = $sms->singleSend()){
            \Yii::$app->Beng->outHtml('发送成功');
        }else{
            \Yii::$app->Beng->outHtml($sms->getMessage());
        }
        ```