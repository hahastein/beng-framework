<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;
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
            'source_id',
            'video_url',
            'cover_image',
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
            $query->joinWith(['article'])->where([
                self::tableName().'.user_id' =>$user_id,
                self::tableName().'.module' => Enum::MODULE_TYPE_ARTICLE
            ])->asArray();
        });
    }
}