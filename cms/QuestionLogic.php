<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Modules;

/**
 * 问题逻辑处理
 * Class QuestionLogic
 * @package bengbeng\framework\cms
 */
class QuestionLogic extends Modules
{

    private $questionID;

    protected function init()
    {
        parent::init();
        $this->questionID = \Yii::$app->request->post('questionid', 0);

    }

    /**
     * 设置问答参数
     * @param integer $questionID
     */
    public function setQuestionID($questionID)
    {
        $this->questionID = $questionID;
    }
}