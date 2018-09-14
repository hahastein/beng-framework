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
 * @property integer $goods_id 商品ID
 * @property string $goods_name 商品名称
 * @property string $goods_slogan 商品广告语
 *
 * @property integer $store_id 所属商户ID，如果为单商户电商，默认为0
 * @property string $store_name 商户名称
 * @package bengbeng\framework\models\goods
 */
class GoodsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return "{{%goods}}";
    }
}