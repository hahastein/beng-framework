<?php


namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;

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
        return $this->hasOne(ArticleARModel::className(),['article_id'=>'object_id']);
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
        return self::find()->joinWith(['article'])->where([
            self::tableName().'user_id' =>$user_id,
            'module' => Enum::MODULE_TYPE_ARTICLE
        ])->asArray()->all();
    }
}