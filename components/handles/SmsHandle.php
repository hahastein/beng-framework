<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/16
 * Time: 16:47
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\components\driver\sms\SmsDriverAbstract;
use bengbeng\framework\components\helpers\NullHelper;
use bengbeng\framework\models\SmsARModel;
use yii\db\Exception;
use yii\helpers\Json;
use Yunpian\Sdk\YunpianClient;
use Yunpian\Sdk\YunpianConf;

class SmsHandle
{

    /**
     * 登录验证码
     */
    const SMS_TYPE_LOGIN = 1;

    /**
     * 注册验证码
     */
    const SMS_TYPE_REG = 2;

    /**
     * 解绑验证码
     */
    const SMS_TYPE_UNBIND = 3;

    /**
     * 其它
     */
    const SMS_TYPE_MORE = 4;

    /**
     * @var array 配置文件
     */
    private $config;
    /**
     * @var bool|string 命名空间
     */
    public $namespace;

    public $message;

    private $model;

    private $phone;

    public function __construct($phone)
    {
        $this->config = \Yii::$app->params['smsConfig'];
        $this->namespace = NullHelper::arrayKey($this->config, 'namespace');
        $this->phone = $phone;
        $this->model = new SmsARModel();
    }

    /**
     * 发送验证码
     * @param integer $smsType 短信验证码类型
     * @param int $templateID 模板ID
     * @return bool
     */
    public function singleSend($smsType = self::SMS_TYPE_LOGIN, $templateID = 0){
        if(!isset($this->config) || !is_array($this->config)){
            $this->message = '没有找到发送短信的配置或者配置文件格式不正确';
            return false;
        }

        if(!$this->phone){
            $this->message = '电话号格式不正确';
            return false;
        }

        //生成一个验证码
        $send_code = sprintf("%06d", rand(0,999999));

        try{
            return $this->save($send_code, $smsType, $templateID);
        }catch (Exception $ex){
            $this->message = $ex->getMessage();
            return false;
        }
    }

    /**
     * @param $code
     * @param $smsType
     * @param $templateID
     * @return array|mixed
     * @throws Exception
     */
    private function save($code, $smsType, $templateID){

        $this->model->setAttributes(['phone_num' => $this->phone]);
        if(!$this->model->validate()) {
            throw new Exception(current($this->model->getFirstErrors()));
        }

        $smsInfo = $this->model->info([
            'phone_num' => $this->phone,
            'sms_type' => $smsType
        ]);

        if(isset($smsInfo)){
            if ($smsInfo->addtime+60 > time()) {
                throw new Exception('您操作太频繁了,稍后再试');
            }
        }


        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $this->model->phone_num = $this->phone;
            $this->model->sms_content = $this->contentFormat();
            $this->model->sms_type = $smsType;
            $this->model->sms_template = $templateID;
            $this->model->sms_number = $code;
            $this->model->addtime = time();

            if(!$this->model->save()){
                throw new Exception('发送异常。');
            }

            if($smsDriver = $this->setDriver()){
                if($smsDriver->singleSend($this->phone, $code, $templateID)){
                    $transaction->commit();
                    return $code;
                }else{
                    throw new Exception($smsDriver->getMessage());
                }
            }else{
                throw new Exception('驱动配置出错，请检查是否正确');
            }
        }catch (Exception $ex){
            $transaction->rollback();
            throw $ex;
        }

    }

    /**
     * 设置上传驱动
     * @return SmsDriverAbstract|bool
     */
    private function setDriver(){
        $driver = $this->config['driver'];

        if(!$this->namespace){
            $this->namespace = '\\bengbeng\\framework\\components\\driver\\sms\\';
        }

        $class = $this->namespace.ucfirst($driver).'Driver';
        var_dump($class);die;
        if(class_exists($class)){
            return new $class($this->config);
        }else{
            return false;
        }
    }

    private function contentFormat(){
        if(is_array($this->config['content'])){
            return Json::encode($this->config['content']);
        }else{
            return $this->config['content'];
        }
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $param
     * @return bool
     * @throws \Exception
     */
    public static function validateSmsCode($param){
        $smsModel = new SmsARModel();
        $smsInfo = $smsModel->findByCode($param['phone_num'], $param['code']);
        if(!$smsInfo)throw new \Exception('验证码错误');
        if($smsInfo->addtime + 60 < time()){
            throw new \Exception('验证码过时');
        }
        return true;
    }

    const SMS_STATUS_USE = 1;
    const SMS_STATUS_NOUSE = 0;

    /**
     * 更新验证码使用情况
     * @param $phone_num
     * @param $send_code
     * @param int $status
     * @return bool
     */
    public static function status($phone_num, $send_code, $status = self::SMS_STATUS_USE){

        $model = new SmsARModel();
        if($smsInfo = $model->info([
            'phone_num' => $phone_num,
            'is_use' => $status==self::SMS_STATUS_USE?self::SMS_STATUS_NOUSE:self::SMS_STATUS_USE,
            'sms_number' => $send_code
        ])){

            return $model->updateAll([
                'is_use' => $status,
                'lasttime' => time()
            ], [
                'auto_id' => $smsInfo->auto_id
            ]);

        }else{
            return false;
        }
    }

}