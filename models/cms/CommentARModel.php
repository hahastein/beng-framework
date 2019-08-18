<?php


namespace bengbeng\framework\models\cms;


use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\UserARModel;
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

    public function getUser(){
        return $this->hasOne(UserARModel::className(),['user_id'=>'user_id'])->select([
            'nickname',
            'avatar_head',
            'user_id'
        ]);
    }

    public function getArticle(){
        return $this->hasOne(ArticleARModel::className(),['article_id'=>'object_id'])->select([
            'article_id',
            'url_code',
            'title',
            'view_count',
            'comment_count',
            'share_count',
            'source_id',
            'video_url',
            'cover_image',
            'createtime'
        ]);
    }

    public function findAllByObjectID($mode, $objectID){
        return $this->dataSet(function (ActiveQuery $query) use($mode, $objectID){
            $query->with(['user']);
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

    public function findByArticle($user_id){

        return $this->dataSet(function (ActiveQuery $query) use($user_id){
            $query->joinWith(['user', 'article']);
            $query->where([
                self::tableName().'.comment_module' => Enum::MODULE_TYPE_ARTICLE,
                self::tableName().'.status' => 10,
                self::tableName().'.parent_id' => 0,
                self::tableName().'.user_id' => $user_id
            ]);
            $query->orderBy(['createtime' => SORT_DESC]);
            $query->asArray();
        });
    }
}