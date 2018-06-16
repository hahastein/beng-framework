<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/16
 * Time: 16:10
 */
namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * Class SmsARModel
 * @property integer $auto_id       自增ID
 * @property integer $phone_num     手机号
 * @property string $sms_content    短信发送内容
 * @property integer $sms_type      发送短信类型  1：登录验证
 * @property integer $sms_number
 * @property integer $addtime
 * @property bool $is_use
 * @package bengbeng\framework\models
 */
class SmsARModel extends ActiveRecord{


    public static function tableName(){
        return '{{%user_sms}}';
    }

    /**
     * 获取信息
     * @param bool $where
     * @param int $sort
     * @return array|null|ActiveRecord
     */
    public function info($where = false, $sort = SORT_DESC){
        $query = self::find();
        if($where)$query->where($where);
        $query->orderBy(['auto_id'=>$sort]);
        return $query->one();
    }

}
