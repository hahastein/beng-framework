<?php
namespace bengbeng\framework\components\helpers;

use Yii;

/**
 * 用户表转换工具
 * Class AdminHelper
 * @package bengbeng\framework\components\helpers
 */
class AdminHelper{

    const ADMIN_MANAGE = 0;
    const AGENT_MANAGE = 10;
    const STORE_MANAGE = 11;
    const ADMIN_MANAGE_STRING = '系统管理员';
    const AGENT_MANAGE_STRING = '代理商管理员';
    const STORE_MANAGE_STRING = '商户管理员';
    const ERROR_MANAGE = false;

    public static function getAdminType($is_string = false, $level = ''){
        if($level == '') {
            $level = self::getIdentity("admin_level");
        }
        if($is_string){
            switch ($level) {
                case self::ADMIN_MANAGE:
                    return self::ADMIN_MANAGE_STRING;
                    break;
                case self::AGENT_MANAGE:
                    return self::AGENT_MANAGE_STRING;
                    break;
                case self::STORE_MANAGE:
                    return self::STORE_MANAGE_STRING;
                    break;
                default:
                    return self::ERROR_MANAGE;
                    break;
            }
        }else{
            return $level;
        }
    }

    public static function getAgentID(){
        return self::getAgentAtt("agent_id");
    }

    public static function getSupervisorID(){
        return self::getSupervisorAtt('manage_id');
    }

    public static function getUserInfo($type){
        return self::getIdentity($type);
    }

    private static function getIdentity($type){
        if(isset(Yii::$app->user->identity->$type)) {
            return Yii::$app->user->identity->$type;
        }else{
            return false;
        }
    }

    public static function getAgentAtt($param){

        if(self::getIdentity('agentInfo')) {
            return self::getIdentity('agentInfo')->$param;
        }else{
            return false;
        }
    }

    public static function getSupervisorAtt($param){
        if(self::getIdentity('storeInfo')) {
            return self::getIdentity('storeInfo')->$param;
        }else{
            return false;
        }
    }

}