<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-07
 * Time: 17:37
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use bengbeng\framework\models\UserARModel;
use Overtrue\Socialite\AuthorizeFailedException;
use Yii;
use bengbeng\framework\enum\WeixinEnum;
use EasyWeChat\Factory;

class WeixinHandle
{
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
     * 根据APP返回的CODE获取微信用户信息
     * @return array
     * @throws \Exception
     */
    public static function getWxUnionCode(){

        try {
            $code = Yii::$app->request->get('code');
            if(!isset($code)){
                throw new \RuntimeException("请传入用户CODE...");
            }

            $driver_type = Yii::$app->request->post('driver_type');
            if($driver_type == Enum::DRIVER_TYPE_WXXCX){
                $wxUserInfo = self::miniProgram($code);
            }else{
                $wxUserInfo = self::appProgram();
            }
            return $wxUserInfo;
        }catch (AuthorizeFailedException $ex){
            if(isset($ex->body)){
                throw new \Exception(WeixinEnum::$returnCode[$ex->body['errcode']]);
            }
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @return array
     */
    private static function appProgram(){
        $wechat = Factory::officialAccount(Yii::$app->params['WECHAT']);
        if(!isset($wechat) || !$wechat){
            throw new \RuntimeException("微信初始化失败...");
        }
        $wxUserInfo = $wechat->oauth->user();
        if(!isset($wxUserInfo)){
            throw new \RuntimeException("用户数据获取失败...");
        }

        //获取用户数据
        return [
            'unionid' => $wxUserInfo->getOriginal()['unionid'],
            'openid' => $wxUserInfo->getId(),
            'nickname' => $wxUserInfo->getNickname(),
            'avatar' => $wxUserInfo->getAvatar(),
            'sex' => $wxUserInfo->getOriginal()['sex']
        ];
    }

    /**
     * @param string $code
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    private static function miniProgram($code){
        $iv = Yii::$app->request->post('iv');
        $encryptedData = Yii::$app->request->post('encrypted');
        if(!isset($iv) || !isset($encryptedData)){
            throw new \RuntimeException("参数错误...");
        }
        $wechat = Factory::miniProgram(Yii::$app->params['WECHAT_XCX']);
        if(!isset($wechat) || !$wechat){
            throw new \RuntimeException("微信初始化失败...");
        }
        $sessionData = $wechat->auth->session($code);
        if(!$sessionData || !isset($sessionData['session_key']) || empty($sessionData['session_key'])){
            throw new \RuntimeException("用户数据获取失败...");
        }
        p($code);
        p($sessionData);
        p($encryptedData);
        p($iv);

        $wxUserInfo = $wechat->encryptor->decryptData($sessionData['session_key'], $iv, $encryptedData);
        if(!isset($wxUserInfo)){
            throw new \RuntimeException("用户数据获取失败...");
        }

        //返回获取用户数据
        return [
            'unionid' => $wxUserInfo['unionId'],
            'openid' => $wxUserInfo['openId'],
            'nickname' => $wxUserInfo['nickName'],
            'avatar' => $wxUserInfo['avatarUrl'],
            'sex' => $wxUserInfo['gender']
        ];
    }
}