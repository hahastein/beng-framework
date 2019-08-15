<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;

/**
 * 名人表(包含名人，专家，明星等信息)
 * Class CelebrityARModel
 * @property integer $celebrity_id
 * @property string $celebrity_name
 * @property integer $belong_id 所属ID 可以是所属医院，公司等
 * @property string $belong_name 所属名称 可以是所属医院，公司等
 * @property string $jobs
 * @property string $introduce
 * @property string $head
 * @property string $tag
 * @property string $extend
 * @property integer $createtime
 * @package bengbeng\framework\models\cms
 */
class CelebrityARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_celebrity}}';
    }
}