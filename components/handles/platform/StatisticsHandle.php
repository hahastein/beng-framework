<?php
/**
 * Created by PhpStorm.
 * User: bengbeng
 * Date: 2018/8/19
 * Time: 上午10:16
 */

namespace bengbeng\framework\components\handles\platform;


use bengbeng\framework\base\Enum;
use bengbeng\framework\models\PlatformStatisticsARModel;

class StatisticsHandle
{
    private $_model;
    public $data;
    public $openCache;

    public function __construct()
    {
        $this->openCache = true;
        $this->_model = new PlatformStatisticsARModel();
        $this->data = $this->_model->dataSet();
    }

    public function getStatistics($column){

        return array_filter($this->data, function ($key) use ($column){
            return $key == $column;
        });

//        switch ($column){
//            case Enum::PLATFORM_STATISTICS_ACCOUNT_AMOUNT:
//                return '';
//            default:
//                return '';
//        }
    }

}