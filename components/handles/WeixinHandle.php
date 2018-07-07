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
     * 根据APP返回的CODE获取微信用户信息
     * @param int $idType
     * @param bool $isSave
     * @return array
     * @throws \Exception
     */
    public static function getWxUnionCode($idType = WeixinEnum::WXID_TYPE_UNIONID, $isSave = false){

        try {
            $code = Yii::$app->request->get('code');
            if(!isset($code)){
                throw new \RuntimeException("请传入用户CODE...");
            }

            $driver_type = Yii::$app->request->post('driver_type');
            if($driver_type == Enum::DRIVER_TYPE_WX){
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
                $wxUserInfo = $wechat->encryptor->decryptData($sessionData['session_key'], $iv, $encryptedData);
                if(!isset($wxUserInfo)){
                    throw new \RuntimeException("用户数据获取失败...");
                }

                p($wxUserInfo);die;

                //获取用户数据
                $userID = 0;
                $nickName = '';
                $avatar = '';
                $sex = '';

            }else{
                $wechat = Factory::officialAccount(Yii::$app->params['WECHAT']);
                if(!isset($wechat) || !$wechat){
                    throw new \RuntimeException("微信初始化失败...");
                }
                $wxUserInfo = $wechat->oauth->user();
                if(!isset($wxUserInfo)){
                    throw new \RuntimeException("用户数据获取失败...");
                }

                //获取用户数据
                $userID = $idType == WeixinEnum::WXID_TYPE_UNIONID?$wxUserInfo->getOriginal()['unionid']:$wxUserInfo->getId();
                $nickName = $wxUserInfo->getNickname();
                $avatar = $wxUserInfo->getAvatar();
                $sex = $wxUserInfo->getOriginal()['sex'];
            }



//            if($isSave){
            //如果设置出入，请增加存入流程
//            }

            return [
                'id' => $userID,
                'nickname' => $nickName,
                'avatar' => $avatar,
                'sex' => $sex
            ];

        }catch (AuthorizeFailedException $ex){
            if(isset($ex->body)){
                throw new \Exception(WeixinEnum::$returnCode[$ex->body['errcode']]);
//                return [$ex->body['errcode'], $ex->body['errmsg']];
            }
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
     * 验证微信登录
     * @param UserARModel $model
     * @param string $code
     * @return array
     * @throws \Exception
     */
    public static function validateWeixin($model, $code = ""){
        try{
            $wxInfo = self::getWxUnionCode();
            $userInfo = $model->findByWxunion($wxInfo['id']);
            return $userInfo;
        }catch (\Exception $ex){
            throw $ex;
        }
    }
}