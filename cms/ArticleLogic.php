<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;
use yii\db\ActiveQuery;

/**
 * 文章系统
 * Class ArticleLogic
 * @package bengbeng\framework\cms
 */
class ArticleLogic extends CmsBase
{

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new ArticleARModel();
        $this->moduleModel->showField = [
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
        ];
        $this->moduleModel->with = ['celebrity'];
    }

    /**
     * 获取推荐
     */
    public function recommend(){
        if($this->getUserID()){
            $this->moduleModel->with['fav'] = function(ActiveQuery $query){
                $query->where([
                    'module' => Enum::MODULE_TYPE_ARTICLE,
                    'user_id' => $this->getUserID()
                ]);
            };
        }
        $articleData = $this->moduleModel->findRecommendByFilter();
        return $this->parseDataAll($articleData);
    }

    /**
     * 获取所有数据
     */
    public function all(){
        if($this->getUserID()){
            $this->moduleModel->with['fav'] = function(ActiveQuery $query){
                $query->where([
                    'module' => Enum::MODULE_TYPE_ARTICLE,
                    'user_id' => $this->getUserID()
                ]);
            };
        }
        $articleData = $this->moduleModel->findAllByCateID($this->cateID);
        return $this->parseDataAll($articleData);
    }

    public function search($keyword){
        $articleData = $this->moduleModel->findAllByKeyword($keyword);
        return $this->parseDataAll($articleData);
    }

    /**
     * 获取详情
     */
    public function info(){
        $this->moduleModel->with=[];
//        var_dump($this->getUserID());die;
        $this->moduleModel->showField = ['article_id', 'title','source_id','user_id', 'url_code','comment_count','fav_count','share_count','video_url','cover_image'];
        if($this->getUserID()){
            $this->moduleModel->with['fav'] = function(ActiveQuery $query){
                $query->where([
                    'module' => Enum::MODULE_TYPE_ARTICLE,
                    'user_id' => $this->getUserID()
                ]);
            };
        }
        $articleData = $this->moduleModel->findOneByArticleID($this->articleID);
//        var_dump($articleData);die;
        return $this->parseDataOne($articleData);
    }

    /**
     * 获取评论
     */
    public function comment(){


    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);

        if(isset($item['app_content'])){
            $item['app_content'] = unserialize( $item['app_content'] );
        }
        $item['h5_url'] = \Yii::getAlias('@hybridUrl').'/expert/'.$item['url_code'];
        if(isset($item['video_url']) && !empty($item['video_url'])){
            $item['video_url'] = \Yii::getAlias('@cdnUrl').'/'.$item['video_url'];
        }
        if(isset($item['fav'])){
            $item['fav'] = 1;
        }else{
            $item['fav'] = 0;
        }

        return $item;
    }

}