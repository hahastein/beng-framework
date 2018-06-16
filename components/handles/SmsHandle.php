<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/16
 * Time: 16:47
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\SmsARModel;
use yii\db\Exception;
use Yunpian\Sdk\YunpianClient;
use Yunpian\Sdk\YunpianConf;

class SmsHandle
{

    const SMS_TYPE_LOGIN = 1;
    const SMS_TYPE_REG = 2;

    private static $sms_content = "【%s】您的验证码是%u";

    /**
     * @param $phone_num
     * @param int $sms_type
     * @return array
     * @throws Exception
     */
    public static function send($phone_num, $sms_type = self::SMS_TYPE_LOGIN){

        $smsConfig = \Yii::$app->params['smsConfig'];
        if(!isset($smsConfig)){
            return [400,'没有找到发送短信的配置'];
        }

        $send_code = sprintf("%06d", rand(0,999999));
        $send_title = "竹迹脉金所";

        $content = sprintf(self::$sms_content, $send_title,$send_code);

        $model = new SmsARModel();
        $smsInfo = $model->info([
            'phone_num' => $phone_num,
            'sms_type' => self::SMS_TYPE_LOGIN
        ]);

        if(isset($smsInfo)){
            if ($smsInfo->addtime+60 > time()) {
                return [400,'您操作太频繁了,稍后再试'];
            }
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $model->phone_num = $phone_num;
            $model->sms_content = $content;
            $model->sms_type = $sms_type;
            $model->sms_number = $send_code;
            $model->addtime = time();

            if(!$model->save()){
                throw new Exception('发送异常。');
            }

            if($smsConfig['use'] == 'YunPian'){

                self::YunPianSend($phone_num, $content, $smsConfig, function ($result, $message) use ($transaction){
                    if($result){
                        $transaction->commit();
                        return [200, $message];
                    }else{
                        \Yii::$app->B->outHtml($message['msg']);
                        throw new Exception($message->msg);
                    }
                });


//                if(self::YunPianSend($phone_num, $content, $smsConfig)){
//                    return [200, '发送成功'];
//                }else{
//                    throw new Exception('发送失败，网络问题');
//                }
            }else{
                throw new Exception('请配置发送短信的类型');
            }
        }catch (Exception $ex){
            $transaction->rollback();
            return [400,$ex->getMessage()];
        }

    }

    /**
     * 云片发送
     * @param $phone_num
     * @param $content
     * @param $smsConfig
     * @param \Closure $closure
     * @return mixed
     */
    private static function YunPianSend($phone_num, $content, $smsConfig, \Closure $closure){
        if($smsConfig['https']){
            $yunpian = YunpianClient::create($smsConfig['key']);
        }else{
            $yunpian = YunpianClient::create($smsConfig['key'],[
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
        try{
            $yunpian_send = $yunpian->sms()->single_send([
                YunpianClient::MOBILE => $phone_num,
                YunpianClient::TEXT => $content
            ]);
            if($yunpian_send->isSucc()){
                return call_user_func($closure,true,"发送成功");
            }else{
                return call_user_func($closure,false,$yunpian_send);
            }
        }catch (\Exception $ex){
            return call_user_func($closure, false, $ex->getMessage());
        }

    }

}