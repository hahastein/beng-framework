<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 16:02
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\AreaARModel;
use bengbeng\framework\models\IndustryARModel;
use bengbeng\framework\models\TagARModel;

class ResourceHandle
{
    public static function findAreaAll(){
        return self::findArea();
    }

    /**
     * 查找某个城市下的所有区域
     * @param $cityID
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAreaByCity($cityID){
        return self::findArea([
            'parent_id' => $cityID
        ]);
    }

    /**
     * 查找全部省份及直辖市，不包含子城市
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAreaByCountry(){
        return self::findArea([
            'parent_id' => 0
        ]);
    }

    /**
     * 增量查找新增城市
     * @param $preUpdateTime
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAreaByIncrement($preUpdateTime){
        return self::findArea([
            'between', $preUpdateTime, time()
        ]);
    }

    /**
     * @param bool $where
     * @return array|\yii\db\ActiveRecord[]
     */
    private static function findArea($where = false){
        $model = new AreaARModel();
        $query = $model->find();
        if($where){
            $query->where($where);
        }
        return $query->all();
    }

    public static function Category(){

    }

    public static function findTagAll(){
        $model = new TagARModel();
        return $model->data();
    }

    public static function findIndustryAll(){
        $model = new IndustryARModel();
        return $model->data([
            'status' => 1
        ]);
    }
}