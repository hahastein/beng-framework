<?php
/**
 * Created by PhpStorm.
 * User: bengbeng
 * Date: 2018/8/19
 * Time: 上午10:16
 */

namespace bengbeng\framework\components\handles\platform;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\PlatformStatisticsARModel;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class StatisticsHandle
{
    private $_model;
    public $data;
    public $openCache;

    public function __construct()
    {
        $this->openCache = true;
        $this->_model = new PlatformStatisticsARModel();
        $this->data = $this->_model->dataSet(function (ActiveQuery $query){
            $query->asArray();
        });

    }

    public function getStatistics($column){
        return array_filter($this->data, function ($item) use ($column){
            return $item['model_name'] == $column;
        })[0];

    }

    public function saveModel($column, $updateValue, $des){
        return $this->_model->dataUpdate(function (ActiveOperate $operate) use($column,$updateValue,$des){
            $operate->where(['model_name' => $column]);
            switch ($column){
                case Enum::PLATFORM_STATISTICS_ACCOUNT_AMOUNT:
                    $operate->params([
                        'model_int' => new Expression('model_int + '.$updateValue),
                        'lasttime' => time(),
                        'des' => empty($des)?'向平台增加金额：'.$updateValue.'元':$des
                    ]);
                    break;
                default:
                    $operate->params([
                        'model_value' => $updateValue,
                        'lasttime' => time(),
//                        'des' => empty($des)?'向平台增加金额：'.$updateValue.'元':$des
                    ]);
                    break;
            }

        });
    }

}