<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018-06-12
 * Time: 18:04
 */

namespace bengbeng\components\handle;
use bengbeng\framework\models\UserARModel;
use yii\db\Exception;
use EasyWeChat\Foundation\Application;
use Overtrue\Socialite\AuthorizeFailedException;


class UserHandle{

    const LOGIN_TYPE_ACCOUNT = 10;
    const LOGIN_TYPE_SMS = 20;
    const LOGIN_TYPE_WEIXIN = 30;

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
     * @param $code
     * @return string
     */
    public static function getWxUnionCode($code, $isSave = false){
        try {
            $wechatConfig = new Application(Yii::$app->params['WECHAT']);
            $wxUserInfo = $wechatConfig->oauth->user();

            if(!isset($wxUserInfo){

            }
            if($isSave){
                //如果设置出入，请增加存入流程
            }

        }catch (AuthorizeFailedException $ex){

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