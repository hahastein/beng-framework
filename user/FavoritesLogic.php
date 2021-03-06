<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\ArticleARModel;
use bengbeng\framework\models\cms\QuestionsARModel;
use bengbeng\framework\models\goods\GoodsARModel;
use bengbeng\framework\models\UserFavoritesARModel;
use yii\db\Exception;
use yii\db\StaleObjectException;

class FavoritesLogic extends UserBase
{

    private $isCancel = false;

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new UserFavoritesARModel();
    }

    /**
     * 收藏文章
     * @param int $article_id 文章ID，如果不传，则获取post或者get的id参数
     * @param  bool $autoDelete
     * @return bool
     */
    public function article($article_id = 0, $autoDelete = true){

        $article_id = $article_id>0?$article_id:\Yii::$app->request->post('id', 0);

        try{
            if($article_id <= 0  || !$this->getUserID()){
                throw new Exception('参数错误');
            }

            $articleModel = new ArticleARModel();
            if(!$articleModel->exists($article_id)){
                throw new Exception('文章不存在');
            }

            return $this->save($article_id,Enum::MODULE_TYPE_ARTICLE, $autoDelete);

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }

    }

    public function question($question_id = 0, $autoDelete = true){
        $question_id = $question_id>0?$question_id:\Yii::$app->request->post('question_id', 0);
        try{
            if($question_id <= 0  || !$this->getUserID()){
                throw new Exception('参数错误');
            }

            $questionModel = new QuestionsARModel();
            if(!$questionModel->exists($question_id)){
                throw new Exception('文章不存在');
            }

            return $this->save($question_id,Enum::MODULE_TYPE_FAQS, $autoDelete);

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function goods($goods_id = 0, $autoDelete = true){
        $goods_id = $goods_id>0?$goods_id:\Yii::$app->request->post('goods_id', 0);
        try{
            if($goods_id <= 0  || !$this->getUserID()){
                throw new Exception('参数错误');
            }

            $goodsModel = new GoodsARModel();
            if(!$goodsModel->exists($goods_id)){
                throw new Exception('商品不存在');
            }

            return $this->save($goods_id,Enum::MODULE_TYPE_GOODS, $autoDelete);

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * @param $id
     * @param $type
     * @param bool $autoDelete
     * @return bool
     * @throws Exception
     */
    private function save($id, $type, $autoDelete = true){
        try{
            if($deleteModel = $this->moduleModel->findByModuleAndID($id, $this->getUserID(), $type)){
                if($autoDelete){
                    if($deleteModel->delete()){
                        $this->isCancel = true;
                        return true;
                    }else{
                        throw new Exception('取消收藏失败');
                    }
                }else{
                    throw new Exception('此文章已经收藏');
                }
            }else{
                //写入收藏表
                $this->moduleModel->object_id = $id;
                $this->moduleModel->user_id = $this->getUserID();
                $this->moduleModel->module = $type;
                $this->moduleModel->createtime = time();

                if($this->moduleModel->save()){
                    $this->isCancel = false;
                    return true;
                }else{
                    throw new Exception('收藏失败');
                }
            }
        }catch (Exception $ex){
            throw new Exception($ex->getMessage());
        }catch (\Throwable $ex){
            throw new Exception($ex->getMessage());
        }
    }

    public function getIsCancel(){
        return $this->isCancel;
    }
}