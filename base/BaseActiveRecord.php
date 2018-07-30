<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/7/11
 * Time: 21:09
 */

namespace bengbeng\framework\base;

use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BaseActiveRecord extends ActiveRecord
{
    public $pageSize = 20;
    public $validatePage = true;

    private $pagination;
    private $dataCount = 0;

    public function info($where = false){
        return self::dataOne(function (ActiveQuery $query) use($where){
            $query->where($where);
        });
    }

    /**
     * 通用返回单条数据，如需要设定其他参数，请参看参数说明
     * @param \Closure|null $callback 回调方法，返回ActiveQuery类型的$query
     * @return array|null|ActiveRecord
     */
    public function dataOne(\Closure $callback = null){
        $query = self::find();
        if($callback){
            call_user_func($callback, $query);
        }
        return $query->one();
    }

    /**
     * 通用返回数据集合，如需要设定其他参数，请参看参数说明
     * @param \Closure $callback 回调方法，返回ActiveQuery类型的$query
     * 如需自定义设置翻页数量，请在params.php中设置Page参数的PageSize属性
     * 调用参照实例如下:
     * customer = new Customer();
     * customer->pageSize = 30;        设置每页显示数量
     * customer->validatePage = false; 设置最后不返回数据
     * customer->dataSet(function(\yii\db\ActiveQuery $query){
     *      $query->with('custom');
     *      ...
     * });
     * @return array
     */
    public function dataSet(\Closure $callback = null){
        $query = self::find();
        //获取page的设置 默认为一页显示30;
        $page = isset(\Yii::$app->request->params['Page']['PageSize'])?:$this->pageSize;

        $this->dataCount = $query->count();
        $this->pagination = new Pagination([
            'defaultPageSize' => $page,
            'totalCount' => $this->dataCount,
        ]);
        $this->pagination->validatePage = $this->validatePage;

        if($callback){
            call_user_func($callback, $query);
        }
        $query->offset($this->pagination->offset);
        $query->limit($this->pagination->limit);
        return $query->all();
    }

    /**
     * @return mixed
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return int
     */
    public function getDataCount()
    {
        return $this->dataCount;
    }
}