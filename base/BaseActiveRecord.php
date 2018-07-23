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
    public $pageSize = 20;

    private $pagination;

    public function info($where = false){
        $query = self::find();
        $query->where($where);
        return $query->one();
    }

    /**
     * @param \Closure $closure
     * @return \yii\db\ActiveQuery
     */
    public function dataSet(\Closure $closure = null){
        $query = self::find();
        //获取page的设置 默认为一页显示30;
        $page = isset(\Yii::$app->request->params['Page']['PageSize'])?:$this->pageSize;

        $this->pagination = new Pagination([
            'defaultPageSize' => $page,
            'totalCount' => $query->count(),
        ]);
        if($closure){
            call_user_func($closure, $query);
        }
        $query->offset($this->pagination->offset);
        $query->limit($this->pagination->limit);
        return $query;
    }

    /**
     * @return mixed
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}