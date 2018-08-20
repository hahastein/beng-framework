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
                print_r($this->data);

    }

    public function getStatistics($column){

//        return $this->data;
        return array_filter($this->data, function ($item) use ($column){
            return $item;
        });

//        switch ($column){
//            case Enum::PLATFORM_STATISTICS_ACCOUNT_AMOUNT:
//                return '';
//            default:
//                return '';
//        }
    }

}