<?php


namespace bengbeng\framework\models\cms;

use bengbeng\framework\base\BaseActiveRecord;
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
 * @property string $content_mode 内容模式 C标准文本|U链接跳转
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

    public function findAllByCateID($cate_id = 0){

        return self::dataSet(function (ActiveQuery $query) use ($cate_id){

            $whereParams['post_status '] = 1;
            if($cate_id == 0){
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
}