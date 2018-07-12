<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 21:10
 */

namespace bengbeng\framework\models;
use yii\db\ActiveRecord;

/**
 * 用户模型.
 * 创建者:hahastein
 * 创建时间:2018/1/16 19:38
 * Class UserARModel
 * @package bengbeng\framework\models
 */
class RecordARModel extends ActiveRecord
{
    //关联数据库表名
    public static function tableName()
    {
        return '{{%record}}';
    }

    public function Info($where=false){
        return self::find()->where($where)->one();
    }
}