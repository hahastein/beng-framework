<?php


namespace bengbeng\framework\models\cms;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;
use bengbeng\framework\cms\Cms;
use bengbeng\framework\models\CategoryARModel;
use bengbeng\framework\models\UserFavoritesARModel;
use yii\db\ActiveQuery;

/**
 * CMS-文章模型
 * Class ArticleARModel
 * @property integer $article_id
 * @property string $title 标题
 * @property string $sort_title 短标题或者描述
 * @property integer $user_id 用户ID
 * @property integer $admin_id 后台账号ID
 * @property int $view_count 浏览总数
 * @property int $comment_count 评论总数
 * @property int $share_count 分享总数
 * @property int $orderby 排序
 * @property int $mode 类型10全站20PC30APP
 * @property int $module 模块名称
 * @property int $cate_id 分类ID
 * @property string $content_mode 内容模式 C标准文本|U链接跳转|A应用跳转
 * @property bool $comment_status 评论状态1允许0不允许
 * @property int $post_status 文章状态1已审核0未审核2违规
 * @property bool $recommend 是否推荐到首页
 * @property string $app_content 文章内容APP显示
 * @property string $html_content 文章内容Html显示
 * @property string $source 文章来源
 * @property string $video_url 视频Url
 * @property string cover_image 封面图片Url
 * @property int $createtime 创建时间
 * @property int $updatetime 更新时间
 * @package bengbeng\framework\models\cms
 */
class ArticleARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%cms_article}}';
    }

    public function getCelebrity(){
        return $this->hasOne(CelebrityARModel::className(),['celebrity_id'=>'source_id'])->select([
            'celebrity_id',
            'celebrity_name',
            'belong_name',
            'jobs',
            'introduce',
            'head',
            'tag',
            'extend'
        ]);
    }

    public function getCate(){
        return $this->hasOne(CategoryARModel::className(),['cate_id'=>'cate_id'])->select([
            'cate_id',
            'cate_name'
        ]);
    }

    public function getFav(){
        return $this->hasOne(UserFavoritesARModel::className(),['object_id' => 'article_id']);
    }

    public function exists($article_id){
        return self::find()->where(['article_id' => $article_id])->exists();
    }

    public function findAllByCateID($cate_id = 0){

        return self::dataSet(function (ActiveQuery $query) use ($cate_id){

            $whereParams = ['post_status' => Cms::ARTICLE_STATUS_REVIEWED];
            if($cate_id > 0){
                $whereParams['cate_id'] = $cate_id;
            }

            $query->where($whereParams);
            $query->orderBy([
                'orderby' => SORT_DESC,
                'updatetime' => SORT_DESC
            ]);
            $query->asArray();
        });

    }

    public function findAllByKeyword($keyword){
        return self::dataSet(function (ActiveQuery $query) use ($keyword){

            $whereParams = ['post_status' => Cms::ARTICLE_STATUS_REVIEWED];
            $query->where($whereParams);
            $query->andWhere(['like', 'title', $keyword]);
            $query->orderBy([
                'updatetime' => SORT_DESC
            ]);
            $query->asArray();
        });
    }

    public function findRecommendByFilter(){
        return self::dataSet(function (ActiveQuery $query){

            $whereParams = [
                'post_status' => Cms::ARTICLE_STATUS_REVIEWED,
                'recommend' => 1
            ];

            $query->where($whereParams);
            $query->orderBy([
                'orderby' => SORT_DESC,
                'updatetime' => SORT_DESC
            ]);
            $query->asArray();
        });
    }

    public function findOneByArticleID($article_id){
        return self::dataOne(function (ActiveQuery $query) use ($article_id){

            $whereParams = ['post_status' => Cms::ARTICLE_STATUS_REVIEWED];
            $whereParams['article_id'] = $article_id;

            $query->where($whereParams);
            $query->asArray();
        });
    }
}