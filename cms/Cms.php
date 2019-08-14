<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\BaseModules;

/**
 * CMS内容管理系统入口
 * Class Cms
 * @property ArticleLogic $article
 * @package bengbeng\framework\cms
 */
class Cms extends BaseModules
{

    public function __construct()
    {
        parent::__construct();
        $this->moduleName = 'cms';
    }


    protected function setProperty($class)
    {
        //获取CMS下功能通用POST参数
        $cate_id = 0;
        if(\Yii::$app->request->isPost){
            $cate_id = \Yii::$app->request->post('cate_id', 0);
        }else if(\Yii::$app->request->isGet){
            $cate_id = \Yii::$app->request->post('cate_id', 0);
        }

        $class->setCateID($cate_id);

        return $class;
    }

}