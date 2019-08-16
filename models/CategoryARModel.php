<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 21:04
 */

namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * 类别模型.
 * 创建者:hahastein
 * 创建时间:2018/1/16 1:33
 * Class CategoryARModel
 * @property integer $cate_id
 * @property string $cate_name
 * @property string $cate_code
 * @property integer $cate_sort
 * @property integer $parent_id
 * @property integer $cate_order
 * @property string $cate_icon
 * @property integer $recommend
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 * @property integer $admin_id
 * @package bengbeng\framework\models
 */
class CategoryARModel extends BaseActiveRecord
{

    //关联数据库表名
    public static function tableName()
    {
        return '{{%cms_category}}';
    }

    public function findAllByNewly($version){

        return $this->dataSet(function (ActiveQuery $query) use ($version){
            if($this->showField){
                $query->select($this->showField);
            }
            $query->where([
                'status' => 1
            ]);
            if($version){
                $query->andWhere([
                    '>=', 'updatetime1', $version
                ]);
            }
            $query->asArray();
        });
    }

    public function findAllByParentID($parent_id = 0){

        return $this->dataSet(function (ActiveQuery $query) use ($parent_id){
            if($this->showField){
                $query->select($this->showField);
            }
            $query->where([
                'parent_id' => $parent_id,
                'status' => 1
            ]);
            $query->orderBy(['cate_order'=>SORT_DESC]);
            $query->asArray();
        });
    }

    public function cateInfo($where=false){
        $query = self::find()
            ->where($where)
            ->one();
        return $query;
    }
}