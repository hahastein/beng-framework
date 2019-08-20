<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Bootstrap;

/**
 * CMS内容管理系统入口
 * Class Cms
 * @property ArticleLogic $article
 * @property QuestionLogic $question
 * @property AnswerLogic $answer
 * @package bengbeng\framework\cms
 */
class Cms extends Bootstrap
{

    const ARTICLE_STATUS_REVIEWED = 1;
    const ARTICLE_STATUS_REVIEWING = 0;
    const ARTICLE_STATUS_VIOLATION = 2;

    public function init()
    {
        parent::init();
        $this->moduleName = 'cms';
    }

}