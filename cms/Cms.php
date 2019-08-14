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

}