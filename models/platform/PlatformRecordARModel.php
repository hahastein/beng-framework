<?php
/**
 * Created by PhpStorm.
 * User: bengbeng
 * Date: 2018/8/20
 * Time: 上午11:35
 */

namespace bengbeng\framework\models\platform;

use yii\db\ActiveRecord;

/**
 * Class PlatformRecordARModel
 * @property integer $record_id 记录ID
 * @property integer $record_time 变更时间
 * @property string $before_value 更新前数据
 * @property string $update_value 要更新的数据
 * @property string $des 描述
 * @property integer $record_type 记录类型1金额
 * @property integer $update_user_id 被更新数据的用户ID
 * @property integer $operate_id 后台操作人ID
 * @package bengbeng\framework\models\platform
 */
class PlatformRecordARModel extends ActiveRecord
{
    //关联数据库表名
    public static function tableName()
    {
        return '{{%plat_record}}';
    }
}