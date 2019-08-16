<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * Class CommentARModel
 * @property integer $comment_id
 * @property integer $object_id 模块评论ID
 * @property integer $user_id 用户ID
 * @property integer $parent_id 父级ID
 * @property string $content 评论内容
 * @property integer $comment_module 评论的模块
 * @property integer $approve 赞总数
 * @property integer $createtime 创建时间
 * @property integer $status 状态10正常0删除20违规
 * @property integer $updatetime 更新时间
 * @package bengbeng\framework\models\cms
 */
class CommentARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_comment}}';
    }

    public function findAllByObjectID($mode, $objectID){
        return $this->dataSet(function (ActiveQuery $query) use($mode, $objectID){
            $query->where([
                'comment_module' => $mode,
                'object_id' => $objectID,
                'status' => 10,
                'parent_id' => 0
            ]);
            $query->orderBy(['createtime' => SORT_DESC]);
            $query->asArray();
        });
    }
}