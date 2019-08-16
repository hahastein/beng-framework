<?php


namespace bengbeng\framework\user;


use bengbeng\framework\models\cms\ArticleARModel;
use yii\db\Exception;

class FavoritesLogic extends UserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 收藏文章
     * @param int $article_id 文章ID，如果不传，则获取post或者get的id参数
     */
    public function article($article_id = 0){

        $article_id = $article_id>0?$article_id:\Yii::$app->request->post('id', 0);

        try{
            if($article_id <= 0  || !$this->getUserID()){
                throw new Exception('参数错误');
            }

            $articleModel = new ArticleARModel();
            if(!$articleModel->exists($article_id)){
                throw new Exception('文章不存在');
            }

            //写入收藏表

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }

    }
}