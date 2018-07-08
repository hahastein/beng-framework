<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018-06-12
 * Time: 18:04
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\enum\WeixinEnum;
use bengbeng\framework\models\SmsARModel;
use Yii;
use bengbeng\framework\models\UserARModel;
use yii\db\Exception;

class UserHandle{

    /**
     * 登录入口
     * @param $param
     * @param int $loginType
     * @param \Closure $closure
     * @return mixed
     */
    public static function login($param, $loginType = WeixinEnum::LOGIN_TYPE_MOBILE_SMS, \Closure $closure){

        try{
            $model = new UserARModel();
            self::setValidateScenario($model, $loginType);
            self::validateParamData($model, $param);

            switch ($loginType){
                case WeixinEnum::LOGIN_TYPE_ACCOUNT:
                case WeixinEnum::LOGIN_TYPE_MOBILE_PASS:
                    $userInfo = self::validatePass($model, $param);
                    break;
                case WeixinEnum::LOGIN_TYPE_WEIXIN:
                    $userInfo = self::validateWeixin($model);
                    break;
                case WeixinEnum::LOGIN_TYPE_MOBILE_SMS:
                    $userInfo = self::validateSmsCode($model, $param);
                    break;
                default:
                    throw new \Exception('无此类型的登录方式');
            }

//            return [200, $userInfo];
            return call_user_func($closure, 200, $userInfo);

        }catch (\Exception $ex){
            return call_user_func($closure, 400, $ex->getMessage());
        }

    }

    /**
     * @param int $regType
     * @param $params
     * @return array|string
     * @throws \Exception
     */
    public static function register($regType = 0, $params){

        if($regType == 0){
            $insert  = [
                'wx_unioncode' => $params['unionid'],
                'wx_openid' => $params['openid'],
                'avatar_head' => $params['avatar'],
                'nickname' => $params['nickname'],
                'user_sex' => $params['sex'],
                'username' => '竹迹用户'.time(),
                'addtime' => time()
            ];

            $userModel = new UserARModel();
            $userModel->setAttributes($insert, false);
            if($userModel->save()){
                return Yii::$app->db->getLastInsertID();
            }else{
                throw new \Exception('创建用户失败');
            }
        }else{
            throw new \Exception('没有此创建类型');
        }
    }

    public static function autoRegister(){

    }

    public static function bind(){
        echo "user handle base";
    }


    //私有方法

    /**
     * 验证微信登录
     * @param UserARModel $model
     * @return array|false|\yii\db\ActiveRecord
     * @throws \Exception
     */
    private static function validateWeixin($model){
        try{
            $wxInfo = WeixinHandle::getWxUnionCode();
            if($userInfo = $model->findByWxunion($wxInfo['unionid'])){
                return [
                    'user_id' => $userInfo['user_id'],
                    'nickname' => $userInfo['nickname'],
                    'avatar_head' => $userInfo['avatar_head'],
                    'phone_num' => $userInfo['phone_num'],
                    'phone_bind' => $userInfo['phone_bind']
                ];
            }else{
                $user_id = self::register($regType = 0,$wxInfo);
                return [
                    'user_id' => $user_id,
                    'nickname' => $wxInfo['nickname'],
                    'avatar_head' => $wxInfo['avatar'],
                    'phone_num' => 0,
                    'phone_bind' => 0
                ];
            }
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 验证密码登录
     * @param UserARModel $model
     * @param array $param
     * @return array
     * @throws \Exception
     */
    private static function validatePass($model, $param){
        if(isset($param['username'])) {
            $userInfo = $model->findByUsername($param['username']);
        }else if(isset($param['phone_num'])){
            $userInfo = $model->findByMobilenumber($param['phone_num']);
        }else{
            throw new \Exception('用户不存在或者密码错误');
        }
        if(!Yii::$app->getSecurity()->validatePassword($param['userpass'], $userInfo->userpass)){
            throw new \Exception('用户密码错误');
        }
        return $userInfo;
    }

    /**
     * 验证手机验证码
     * @param UserARModel $model
     * @param array $param
     * @return array
     * @throws \Exception
     */
    private static function validateSmsCode($model, $param){
        $smsModel = new SmsARModel();
        $smsInfo = $smsModel->findByCode($param['phone_num'], $param['code']);
        if(!$smsInfo)throw new \Exception('验证码错误');
        if($smsInfo->addtime + 60 < time()){
            throw new \Exception('验证码过时');
        }
        return $model->findByMobilenumber($param['phone_num']);
    }

    /**
     * @param UserARModel $model
     * @param int $loginType
     */
    private static function setValidateScenario($model,$loginType){
        if($loginType == WeixinEnum::LOGIN_TYPE_MOBILE_SMS)$model->setScenario('sms');
        if($loginType == WeixinEnum::LOGIN_TYPE_MOBILE_PASS)$model->setScenario('pass');
        if($loginType == WeixinEnum::LOGIN_TYPE_ACCOUNT)$model->setScenario('account');
    }

    /**
     * 验证数据完整性
     * @param UserARModel $model
     * @param array $param
     * @return bool true|false
     * @throws \Exception
     */
    public static function validateParamData($model, $param){
        $model->setAttributes($param);
        if(!$model->validate()) {
            throw new \Exception(current($model->getFirstErrors()));
        }
        return true;
    }

    /**
     * 验证手机账号是否存在
     * @param $mobile
     * @return bool
     */
    public static function isExistMobile($mobile){
        return UserARModel::find()->where([
            'phone_num' => $mobile
        ])->exists();
    }



    /**
     * 新增用户信息
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function newAddUserInfo()
    {
        if(True) {
            throw new Exception("错误信息");
        }else {
            return true;
        }
    }

    /**
     * 修改用户信息
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function modifyUserInfo(){
        if(True) {
            throw new Exception("错误信息");
        }else {
            return true;
        }
    }
}