<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 14:53
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * 区域数据模型
 * Class AreaARModel
 * @property int $area_id 区域ID
 * @property string $area_name 区域名称
 * @property int $parent_id 父级ID
 * @property int $depth 深度
 * @property string $full_id 全ID
 * @property string $area_code 电话区号
 * @property string $amap_code 高德对应CODE
 * @property string $baidu_code 百度对应CODE
 * @package bengbeng\framework\models
 */
class AreaARModel extends ActiveRecord
{
    public static function tableName(){
        return '{{%area}}';
    }

    public function getChild(){
        return $this->hasOne(self::className(),['parent_id'=>'area_id']);
    }

}