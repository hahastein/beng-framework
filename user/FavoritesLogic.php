<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;
use bengbeng\framework\models\UserFavoritesARModel;
use yii\db\Exception;

class FavoritesLogic extends UserBase
{
    private $favModel;

    public function __construct()
    {
        parent::__construct();
        $this->favModel = new UserFavoritesARModel();
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

            if($this->favModel->exists($article_id, $this->getUserID(), Enum::MODULE_TYPE_ARTICLE)){
                throw new Exception('此文章已经收藏');
            }

            //写入收藏表
            $this->favModel->object_id = $article_id;
            $this->favModel->user_id = $this->getUserID();
            $this->favModel->module = Enum::MODULE_TYPE_ARTICLE;
            $this->favModel->createtime = time();

            if($this->favModel->save()){
                return true;
            }else{
                throw new Exception('收藏失败');
            }

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }

    }
}