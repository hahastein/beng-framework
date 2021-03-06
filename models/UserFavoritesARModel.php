<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;
use bengbeng\framework\models\cms\QuestionsARModel;
use yii\db\ActiveQuery;

/**
 * Class UserFavoritesARModel
 * @property integer $fav_id
 * @property integer $object_id
 * @property integer $user_id
 * @property integer $module
 * @property integer $createtime
 * @package bengbeng\framework\models
 */
class UserFavoritesARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%user_favorites}}';
    }

    public function getArticle(){
        return $this->hasOne(ArticleARModel::className(),['article_id'=>'object_id'])->select([
            'article_id',
            'url_code',
            'title',
            'view_count',
            'comment_count',
            'share_count',
            'fav_count',
            'source_id',
            'video_url',
            'cover_image',
            'createtime'
        ]);
    }

    public function getQuestion(){
        return $this->hasOne(QuestionsARModel::className(),['question_id'=>'object_id'])->select([
            'question_id',
            'url_code',
            'title',
            'content',
            'user_id',
            'nickname',
            'avatar_head',
            'cate_id',
            'fav_count',
            'view_count',
            'reply_count',
            'share_count',
            'is_reply',
            'show_img',
            'createtime'
        ]);
    }

    public function exists($object_id, $user_id, $module = Enum::MODULE_TYPE_ARTICLE){
        return self::find()->where([
            'object_id' => $object_id,
            'user_id' => $user_id,
            'module' => $module
        ])->exists();
    }

    public function findByModuleAndID($object_id, $user_id, $module = Enum::MODULE_TYPE_ARTICLE){
        return self::find()->where([
            'object_id' => $object_id,
            'user_id' => $user_id,
            'module' => $module
        ])->one();
    }

    public function findByArticle($user_id){

        return self::dataSet(function (ActiveQuery $query) use($user_id){
            $query->joinWith(['article' => function(ActiveQuery $query) use ($user_id){

                $query->with(['celebrity','fav' => function(ActiveQuery $query) use ($user_id){
                    $query->where([
                        'module' => Enum::MODULE_TYPE_ARTICLE,
                        'user_id' => $user_id
                    ]);
                }]);

            }])->where([
                self::tableName().'.user_id' =>$user_id,
                self::tableName().'.module' => Enum::MODULE_TYPE_ARTICLE
            ])->asArray();
        });
    }

    public function findByQuestion($user_id){

        return self::dataSet(function (ActiveQuery $query) use($user_id){
            $query->joinWith(['question' => function(ActiveQuery $query) use ($user_id){

                $query->with(['user','identify.user','fav' => function(ActiveQuery $query) use ($user_id){
                    $query->where([
                        'module' => Enum::MODULE_TYPE_FAQS,
                        'user_id' => $user_id
                    ]);
                }]);

            }])->where([
                self::tableName().'.user_id' =>$user_id,
                self::tableName().'.module' => Enum::MODULE_TYPE_FAQS
            ])->asArray();
        });
    }
}