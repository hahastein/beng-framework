<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018-06-12
 * Time: 18:04
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\SmsARModel;
use EasyWeChat\Factory;
use Yii;
use bengbeng\framework\models\UserARModel;
use yii\db\Exception;

class UserHandle{

    const LOGIN_TYPE_MOBILE_PASS = 10;
    const LOGIN_TYPE_MOBILE_SMS = 20;
    const LOGIN_TYPE_WEIXIN = 30;
    const LOGIN_TYPE_ACCOUNT = 40;

    const BIND_TYPE_USER = 10;

    const WXID_TYPE_UNIONID = 20;
    const WXID_TYPE_OPENID = 10;

    /**
     * 登录入口
     * @param $param
     * @param int $loginType
     * @return array
     */
    public static function login($param, $loginType = self::LOGIN_TYPE_MOBILE_SMS){

        try{
            $model = new UserARModel();
            self::setValidateScenario($model, $loginType);
            self::validateParamData($model, $param);

            switch ($loginType){
                case self::LOGIN_TYPE_ACCOUNT:
                    if(!$userInfo = self::validatePass($model, $param)){
                        throw new \Exception('账号密码错误');
                    }
                    break;
                case self::LOGIN_TYPE_WEIXIN:
                    if(!$userInfo = self::validateWeixin($model)){
                        throw new \Exception('微信账号不存在');
                    }
                    break;
                case self::LOGIN_TYPE_MOBILE_PASS:
                    if(!$userInfo = self::validatePass($model, $param)){
                        throw new \Exception('手机密码错误');
                    }
                    break;
                case self::LOGIN_TYPE_MOBILE_SMS:
                    if(!$userInfo = self::validateSmsCode($model, $param)){
                        throw new \Exception('手机验证码错误');
                    }
                    break;
                default:
                    throw new \Exception('无此类型的登录方式');
            }

            return [200, $userInfo];

        }catch (\Exception $ex){
            return [400, $ex->getMessage()];
        }

    }

    public static function register(){

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
     * @return bool
     * @throws \Exception
     */
    private static function validateWeixin($model){
        try{
            $wxInfo = self::getWxUnionCode();
            $userInfo = $model->findByWxunion($wxInfo['id']);
            return $userInfo && true;
        }catch (\Exception $ex){
            throw $ex;
        }
    }

    /**
     * 验证密码登录
     * @param UserARModel $model
     * @param array $param
     * @return bool
     */
    private static function validatePass($model, $param){
        if(isset($param['username'])) {
            $userInfo = $model->findByUsername($param['username']);
        }else if(isset($param['phone_num'])){
            $userInfo = $model->findByMobilenumber($param['phone_num']);
        }else{
            $userInfo = false;
        }
        return $userInfo && Yii::$app->getSecurity()->validatePassword($param['userpass'], $userInfo->userpass);
    }

    /**
     * 验证手机验证码
     * @param UserARModel $model
     * @param array $param
     * @return bool
     */
    private static function validateSmsCode($model, $param){
        $smsModel = new SmsARModel();
        $smsInfo = $smsModel->findByCode($param['phone_num'], $param['code']);
        $userInfo = $model->findByMobilenumber($param['phone_num']);
        return $smsInfo && $userInfo;
    }

    /**
     * @param UserARModel $model
     * @param int $loginType
     */
    private static function setValidateScenario($model,$loginType){
        if($loginType == self::LOGIN_TYPE_MOBILE_SMS)$model->setScenario('sms');
        if($loginType == self::LOGIN_TYPE_MOBILE_PASS)$model->setScenario('pass');
        if($loginType == self::LOGIN_TYPE_ACCOUNT)$model->setScenario('account');
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
     * 根据APP返回的CODE获取微信用户信息
     * @param int $idType
     * @param bool $isSave
     * @return array
     * @throws \Exception
     */
    public static function getWxUnionCode($idType = self::WXID_TYPE_UNIONID, $isSave = false){

        try {
            $code = Yii::$app->request->get('code');
            if(!isset($code)){
                throw new \Exception("请传入用户CODE...");
            }

            $wechatConfig = Factory::officialAccount(Yii::$app->params['WECHAT']);
            $wxUserInfo = $wechatConfig->oauth->user();

            if(!isset($wxUserInfo)){
                throw new \Exception("插件初始化出现问题...");
            }

//            if($isSave){
                //如果设置出入，请增加存入流程
//            }

            return [
                'id' => $idType == self::WXID_TYPE_UNIONID?$wxUserInfo->getOriginal()['unionid']:$wxUserInfo->getId(),
                'nickname' => $wxUserInfo->getNickname(),
                'avatar' => $wxUserInfo->getAvatar(),
                'sex' => $wxUserInfo->getOriginal()['sex']
            ];

        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * 验证微信账户是否存在
     * @param $unionCode
     * @return bool
     */
    public static function isExistWxUser($unionCode){
        return UserARModel::find()->where([
            'wx_unioncode' => $unionCode
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