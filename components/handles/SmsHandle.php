<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/16
 * Time: 16:47
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\SmsARModel;
use Yunpian\Sdk\YunpianClient;

class SmsHandle
{

    const SMS_TYPE_LOGIN = 1;
    const SMS_TYPE_REG = 2;

    private static $sms_content = "【%s】您的验证码是%u";

    public static function send($phone_num, $sms_type = self::SMS_TYPE_LOGIN){

        $send_code = sprintf("%06d", rand(0,999999));
        $send_title = "竹迹脉金所";

        $content = sprintf(self::$sms_content, $send_title,$send_code);

        $model = new SmsARModel();
        $smsInfo = $model->info([
            'phone_num' => $phone_num,
            'sms_type' => self::SMS_TYPE_LOGIN
        ]);

        if(!empty($smsInfo->addtime)){
            if ($smsInfo->addtime+60 > time()) {
                return [400,'您操作太频繁了,稍后再试'];
            }
        }

        $model->phone_num = $phone_num;
        $model->sms_content = $content;
        $model->sms_type = $sms_type;
        $model->sms_number = $send_code;
        $model->addtime = time();

        if(!$model->save()){
            return [400,'A呀,报错了'];
        }

        $yunpian = YunpianClient::create('7c57761c85f5bacde151e7362c4031d7');
        $yunpian_send = $yunpian->sms()->single_send([
            YunpianClient::MOBILE => $phone_num,
            YunpianClient::TEXT => $content
        ]);
        if($yunpian_send->isSucc()){
            return [200,'发送成功'];
        }else{
            return [400,'发送失败',$yunpian_send->data()];
        }

    }

}