<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/2
 * Time: 1:48
 */

namespace bengbeng\framework\models\goods;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\AttachmentARModel;
use yii\db\ActiveQuery;

/**
 * 商品主表
 * Class GoodsARModel
 * @property integer $goods_id 商品ID
 * @property string $goods_name 商品名称
 * @property string $goods_slogan 商品广告语
 * @property integer $goods_commonid 商品公共ID
 * @property integer $cate_id 当前分类ID
 * @property integer $cate_id_1 一级分类ID
 * @property integer $cate_id_2 二级分类ID
 * @property integer $cate_id_3 三级分类ID
 * @property string $cate_name 分类名称
 * @property integer $store_id 所属商户ID，如果为单商户电商，默认为0
 * @property string $store_name 商户名称
 * @property integer $brand_id 品牌id
 * @property string $brand_name 品牌名称
 * @property string $html_content html端内容
 * @property string $app_content app端内容
 * @property integer $goods_state 商品状态 0下架，1正常，10违规（禁售）
 * @property integer $goods_verify 商品审核 1通过，0未通过，10审核中
 * @property integer $goods_lock 商品锁定 0未锁，1已锁
 * @property integer $pay_mode 0正常金额10积分
 * @property integer $goods_addtime 商品添加时间
 * @property integer $goods_selltime 上架时间
 * @property integer $goods_storage 商品库存
 * @property float $goods_price 商品价格
 * @property float $goods_marketprice 市场价
 * @property float $goods_costprice 成本价
 * @property integer $goods_integral 消耗积分
 * @package bengbeng\framework\models\goods
 */
class GoodsARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return "{{%shop_goods}}";
    }

    public function getImages(){
        return $this->hasMany(AttachmentARModel::className(),['object_id'=>'goods_id'])->where(['att_type' => Enum::MODULE_TYPE_GOODS])->select([
            'object_id', 'obj_url', 'is_default'
        ]);
    }

    public function getImageDefault(){
        return $this->hasOne(AttachmentARModel::className(),['object_id'=>'goods_id'])->where(['att_type' => Enum::MODULE_TYPE_GOODS, 'is_default' => 1])->select([
            'object_id', 'obj_url', 'is_default'
        ]);
    }

    public function findStatusByAll(){
        return self::dataSet(function (ActiveQuery $query){
            $query->where([
                'goods_state' => 1,
                'goods_verify' => 1,
                'goods_lock' => 0
            ]);
            $query->asArray();
        });
    }
}