<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018-06-12
 * Time: 18:04
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\helpers\NullHelper;
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
     * @param array $params
     * @param int $regType
     * @return array
     * @throws \Exception
     */
    public static function register($params, $regType = Enum::REG_TYPE_MOBILE){

        $userModel = new UserARModel();

        //生成时间
        $createTime = time();
        //生成ID标识
        $userUnionID = $createTime;
        if($regType == Enum::REG_TYPE_MOBILE){
            $insert  = [
                'login_type' => Enum::REG_TYPE_MOBILE,
                'username' => 'App用户'.$userUnionID,
                'phone_num' => $params['phone'],
                'phone_bind' => 1,

            ];
        }else if(Enum::REG_TYPE_WEIXIN){
            $insert  = [
                'login_type' => Enum::REG_TYPE_WEIXIN,
                'wx_openid' => $params['openid'],
                'avatar_head' => $params['avatar'],
                'user_sex' => $params['sex'],
                'wx_bind' => 1
            ];
        }else{
            throw new \Exception('没有此创建类型');
        }

        $insert['driver_type'] = $params['driver_type'];
        $insert['driver_uuid'] = $params['driver_uuid'];
        $insert['addtime'] = $createTime;

        if($username = NullHelper::arrayKey($params, 'username')){
            $insert['username'] = $username;
        }else{
            $insert['username'] = 'wku_' . $createTime;
        }

        if($nickname = NullHelper::arrayKey($params, 'nickname')){
            $insert['nickname'] = $nickname;
        }else{
            $insert['nickname'] = '用户' . $createTime;
        }

        $trans = Yii::$app->db->beginTransaction();
        try{
            $userModel->setAttributes($insert, false);
            if($userModel->save()){
                $userID = Yii::$app->db->getLastInsertID();
                //更新用户的unionid
                $unionID = $userID . '|' . uniqid(md5(microtime(true)),true);
                var_dump($unionID);die;
                $unionID = Yii::$app->getSecurity()->encryptByPassword($unionID, 'bengbeng@2019');
                if($userModel->updateUnionID($userID, $unionID)){
                    $trans->commit();
                    $insert['unionid'] = $unionID;
                    return $insert;
                }else{
                    throw new Exception('创建用户失败[1001]');
                }
            }else{
                throw new Exception('创建用户失败[1000]');
            }
        }catch (Exception $ex){
            $trans->rollBack();
            throw new \Exception($ex->getMessage());
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
                if(self::isExistMobile($params['phone_num'])){
                    throw new \Exception('此手机已经绑定过账号，请使用新的手机号进行绑定');
                }
                $model = new UserARModel();
                if($model = $model->findByWxunion($params['union_id'])){
                    $model->phone_num = $params['phone_num'];
                    $model->phone_bind = 1;
                    return $model->save();
                }else{
                    throw new \Exception('用户不存在');
                }
                break;
            case Enum::USER_BIND_TWOWAY:
                $model = new UserARModel();
                if($model = $model->findByMobileAndWxcode($params['phone_num'], $params['union_id'])){
                    if($model->phone_num && $model->wx_unioncode){
                        throw new \Exception('此用户已经存在');
                    }
                    if(!$model->phone_num){
                        $model->phone_num = $params['phone_num'];
                        $model->phone_bind = 1;
                    }
                    if(!$model->wx_unioncode){
                        $model->wx_unioncode = $params['union_id'];
                        $model->wx_openid = $params['open_id'];
                        $model->wx_bind = 1;
                    }
                }else{
                    $model = new UserARModel();
                    $model->phone_num = $params['phone_num'];
                    $model->phone_bind = 1;
                    $model->wx_unioncode = $params['union_id'];
                    $model->wx_openid = $params['open_id'];
                    $model->wx_bind = 1;
                    $model->avatar_head = $params['avatar'];
                    $model->nickname = $params['nickname'];
                    $model->user_sex = $params['sex'];
                    $model->username = 'App用户'.time();
                    $model->addtime = time();
                }
                if($model->save()){
                    return $model->user_id;
                }else{
                    throw new \Exception('操作失败');
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
//                    return false;
                    return [
                        'user_id' => 0,
                        'union_id' => $wxInfo['unionid'],
                        'nickname' => $wxInfo['nickname'],
                        'avatar_head' => $wxInfo['avatar'],
                        'sex' => $wxInfo['sex'],
                        'openid' => $wxInfo['openid'],
                        'phone_num' => 0,
                        'phone_bind' => 0
                    ];
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