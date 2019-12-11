<?php


namespace bengbeng\framework\models\goods;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class SpellGoodsARModel
 * @property integer $spell_id
 * @property integer $goods_id
 * @property integer $limit_num
 * @property float $spell_price
 * @property float $spell_integral
 * @property integer $spell_pay_mode
 * @property string $spell_name
 * @property string $spell_desc
 * @property integer $spell_start_time
 * @property integer $spell_end_time
 * @property integer $createtime
 * @package bengbeng\framework\models\goods
 */
class SpellGoodsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return "{{%shop_goods_spell}}";
    }

    public function getGoods(){
        return $this->hasMany(GoodsARModel::className(),['goods_id'=>'goods_id']);
    }
}