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
 * @property string $title 头衔（可以是医务头衔，军衔等）
 * @property int $company_id 单位ID
 * @property string $company 单位名称
 * @property int $certificate_id 证明ID（可以是任何证明自己的证书类相关，例如学历，医务级别等）
 * @property int $certificate 证明名字
 * @property int $department_id 部门关联ID
 * @property int $department 部门名称
 * @property string $jobs 职位
 * @property int $sex 性别 1男2女0不知道
 * @property string $introduce 介绍
 * @property string $head 专家头像
 * @property string $tag 标签
 * @property array $extend 扩展字段，json格式
 * @property int $mode 类型 0为系统 1为用户
 * @property int $state 类型 0为删除 1为正常 2审核 4违规
 * @property int $createtime 创建时间
 * @property int $updatetime 修改时间
 * @package bengbeng\framework\models\cms
 */
class CelebrityARModel extends BaseActiveRecord
{
    public static function tableName() {
        return '{{%cms_celebrity}}';
    }

    public function findByModeUser() {
        return $this->dataSet(function (ActiveQuery $query){
            $query->select($this->showField);
            $query->where([

            ]);
            $query->asArray();
        });
    }
}