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

    const WXID_TYPE_UNIONID = 20;
    const WXID_TYPE_OPENID = 10;


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
     * @param $idType
     * @param $isSave
     * @return array
     * @throws \yii\base\Exception
     */
    public static function getWxUnionCode($idType = self::WXID_TYPE_UNIONID, $isSave = false){

        try {
            $code = Yii::$app->request->get('code');
            if(!isset($code)){
                throw new AuthorizeFailedException("请传入用户CODE...");
            }

            $wechatConfig = new Application(Yii::$app->params['WECHAT']);
            $wxUserInfo = $wechatConfig->oauth->user();

            if(!isset($wxUserInfo)){
                throw new AuthorizeFailedException("插件初始化出现问题...");
            }
            if($isSave){
                //如果设置出入，请增加存入流程
            }

            return [
                'id' => $idType == self::WXID_TYPE_UNIONID?$wxUserInfo->getOriginal()['unionid']:$wxUserInfo->getId(),
                'nickname' => $wxUserInfo->getNickname(),
                'avatar' => $wxUserInfo->getAvatar(),
                'sex' => $wxUserInfo->getOriginal()['sex']
            ];

        }catch (AuthorizeFailedException $ex){
            throw new \yii\base\Exception($ex->getMessage());
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