<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\models\cms\ArticleARModel;

/**
 * 文章系统
 * Class ArticleLogic
 * @package bengbeng\framework\cms
 */
class ArticleLogic extends CmsBase
{

    public $articleID;

    public function __construct()
    {
        parent::__construct();
        $this->articleID = \Yii::$app->request->post('articleid', 0);
        $this->moduleModel = new ArticleARModel();
        $this->moduleModel->showField = [
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
        ];
        $this->moduleModel->with = ['celebrity'];
    }

    /**
     * 获取推荐
     */
    public function recommend(){
        $articleData = $this->moduleModel->findRecommendByFilter();
        return $this->parseArticleAll($articleData);
    }

    /**
     * 获取所有数据
     */
    public function all(){
        $articleData = $this->moduleModel->findAllByCateID($this->cateID);
        return $this->parseArticleAll($articleData);
    }

    public function search($keyword){
        $articleData = $this->moduleModel->findAllByKeyword($keyword);
        return $this->parseArticleAll($articleData);
    }

    /**
     * 获取详情
     */
    public function info(){
        $this->moduleModel->showField[] = ['app_content'];
        $articleData = $this->moduleModel->findOneByArticleID($this->articleID);
        return $this->parseArticleOne($articleData);
    }

    /**
     * 获取评论
     */
    public function comment(){


    }

    private function parseArticleAll($data){
        foreach ($data as $key => $item){
            $data[$key] = $this->parseArticleOne($item);
        }
        return $data;
    }

    private function parseArticleOne($article){
        if(!$article){
            return $article;
        }
        $article['createtime'] = date('Y-m-d H:i:s', $article['createtime']);
        if(isset($article['app_content'])){
            $article['app_content'] = unserialize( $article['app_content'] );
        }
        //生成H5地址
        $article['h5_url'] = \Yii::getAlias('@hybridUrl').'/expert/'.$article['url_code'];
        return $article;
    }
}