<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/2
 * Time: 1:48
 */

namespace bengbeng\framework\models\goods;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * 商品主表
 * Class GoodsARModel
 * @package bengbeng\framework\models\goods
 */
class GoodsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return "{{%goods}}";
    }
}