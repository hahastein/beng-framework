<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-09-13
 * Time: 14:04
 */

namespace bengbeng\framework\models\goods;


use bengbeng\framework\base\BaseActiveRecord;

class SubGoodsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return "{{%goods_sub}}"; // TODO: Change the autogenerated stub
    }
}