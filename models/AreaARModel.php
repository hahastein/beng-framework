<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 14:53
 */

namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;
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
class AreaARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%area}}';
    }

    public function getChild(){
        return $this->hasMany(self::className(),['parent_id'=>'area_id']);
    }

    public function findAllByParentID($parent_id = 0){

        return $this->dataSet(function (ActiveQuery $query) use ($parent_id){
            if($this->showField){
                $query->select($this->showField);
            }
            $query->where([
                'parent_id' => $parent_id,
            ]);
            $query->orderBy(['area_order'=>SORT_DESC]);
            $query->asArray();
        });
    }

    public function findAllByRecursion($where = false, $level = 3){
        $model = new AreaARModel();
        $query = $model->find();

        $record = 1;
        $withParam = '';
        while ($record < $level) {
            $withParam .= $record == $level-1?'child':'child.';
            $record++;
        }
        $query->with($withParam);
        if($where){
            $query->where($where);
        }
        $query->andWhere(['parent_id' => 1]);
        return $query->asArray()->all();
    }

}