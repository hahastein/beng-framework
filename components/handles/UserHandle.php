<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018-06-12
 * Time: 18:04
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
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
     * @param bool $autoCreate
     * @param \Closure $closure
     * @return mixed
     */
    public static function login($param, $loginType = WeixinEnum::LOGIN_TYPE_MOBILE_SMS, $autoCreate = true, \Closure $closure){

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
                    $userInfo = self::validateWeixin($model, $autoCreate);
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

    /**
     * 用户绑定信息
     * @param int $type
     * @param $params
     * @return
     * @throws
     */
    public static function bind($type = Enum::USER_BIND_MOBILE, $params){
        switch ($type){
            case Enum::USER_BIND_MOBILE:
                //验证用户是否已经绑定
                $model = new UserARModel();
                if(!$model = $model->findByWxunion($params['unionid'])){
                    $model->phone_num = $params['phone_num'];
                    $model->phone_bind = 1;
                    return $model->save();
                }else{
                    throw new \Exception('此手机已经绑定过账号，请使用新的手机号进行绑定');
                }
                break;
            default:
                throw new \Exception('无此绑定类型');
                break;
        }
    }


    //私有方法

    /**
     * 验证微信登录
     * @param UserARModel $model
     * @param bool $autoCreate
     * @return array|false|\yii\db\ActiveRecord
     * @throws \Exception
     */
    private static function validateWeixin($model, $autoCreate){
        try{
            $wxInfo = WeixinHandle::getWxUnionCode();
            if($userInfo = $model->findByWxunion($wxInfo['unionid'])){
                return [
                    'user_id' => $userInfo['user_id'],
                    'union_id' => $userInfo['wx_unioncode'],
                    'nickname' => $userInfo['nickname'],
                    'avatar_head' => $userInfo['avatar_head'],
                    'phone_num' => $userInfo['phone_num'],
                    'phone_bind' => $userInfo['phone_bind']
                ];
            }else{
                if($autoCreate) {
                    $user_id = self::register($regType = 0, $wxInfo);
                    return [
                        'user_id' => $user_id,
                        'union_id' => $wxInfo['unionid'],
                        'nickname' => $wxInfo['nickname'],
                        'avatar_head' => $wxInfo['avatar'],
                        'phone_num' => 0,
                        'phone_bind' => 0
                    ];
                }else{
                    return false;
                }
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
        if(SmsHandle::validateSmsCode($param)) {
            return $model->findByMobilenumber($param['phone_num']);
        }else{
            throw new \Exception('验证码不存在');
        }
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