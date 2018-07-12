<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 21:04
 */

namespace bengbeng\framework\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * 类别模型.
 * 创建者:hahastein
 * 创建时间:2018/1/16 1:33
 * Class CategoryARModel
 * @package bengbeng\framework\models
 */
class CategoryARModel extends ActiveRecord
{

    //关联数据库表名
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function cateList($where=false){

        $query = self::find()
            ->where($where)
            ->orderBy(['cate_id'=>SORT_DESC]);

        $provider['query'] = $query;

        $dataProvider = new ActiveDataProvider($provider);
        return $dataProvider;
    }

    public function cateInfo($where=false){
        $query = self::find()
            ->where($where)
            ->one();
        return $query;
    }
}