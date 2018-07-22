<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/11
 * Time: 21:09
 */

namespace bengbeng\framework\base;


use yii\data\Pagination;
use yii\db\ActiveRecord;

class BaseActiveRecord extends ActiveRecord
{

    public function info($where = false){
        $query = self::find();
        $query->where($where);
        return $query->one();
    }

    /**
     * @param int $page
     * @return \yii\db\ActiveQuery
     */
    public function dataSet($page = 20){
        $query = self::find();
        //获取page的设置 默认为一页显示30;
        $page = isset(\Yii::$app->request->params['Page']['PageSize'])?:$page;

        $pagination = new Pagination([
            'defaultPageSize' => $page,
            'totalCount' => $query->count(),
        ]);
        $query->offset($pagination->offset);
        $query->limit($pagination->limit);
        return $query;
    }
}