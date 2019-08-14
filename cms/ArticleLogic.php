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
        $this->articleID = 0;
        $this->moduleModel = new ArticleARModel();
    }

    /**
     * 获取推荐
     */
    public function recommend(){

    }

    /**
     * 获取所有数据
     */
    public function all(){
        return $this->moduleModel->findAllByCateID($this->cateID);
    }

    /**
     * 获取详情
     */
    public function info(){

    }

    /**
     * 获取评论
     */
    public function comment(){

    }
}