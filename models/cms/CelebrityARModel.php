<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * 名人表(包含名人，专家，明星等信息)
 * Class CelebrityARModel
 * @property int $celebrity_id
 * @property string $celebrity_name
 * @property int $belong_id 所属ID 可以是所属医院，公司等
 * @property string $belong_name 所属名称 可以是所属医院，公司等
 * @property int $department_id 部门关联ID
 * @property int $department 部门名称
 * @property string $jobs 职位
 * @property int $sex 性别 1男2女0不知道
 * @property string $introduce 介绍
 * @property string $head 专家头像
 * @property string $tag 标签
 * @property array $extend 扩展字段，json格式
 * @property int $mode 类型 0为系统 1为用户
 * @property int $createtime
 * @package bengbeng\framework\models\cms
 */
class CelebrityARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_celebrity}}';
    }

    public function findByModeUser(){
        return $this->dataSet(function (ActiveQuery $query){
            $query->select($this->showField);
            $query->where();
            $query->asArray();
        });
    }
}