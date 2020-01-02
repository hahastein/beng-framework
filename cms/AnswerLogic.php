<?php


namespace bengbeng\framework\cms;


use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\AnswersARModel;

class AnswerLogic extends CmsBase
{
    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new AnswersARModel();
    }

    public function all($celebrity_id = 0)
    {
        if($celebrity_id){
            $answerData = $this->moduleModel->findAllByQuestionAndUserID($this->questionID, $celebrity_id);
        }else{
            $answerData = $this->moduleModel->findAllByQuestionID($this->questionID);
        }
        return $this->parseDataAll($answerData);
    }

    public function allOld($celebrity_id = false, $isImage = false)
    {

        if($isImage){
            $this->moduleModel->with = ['images'];
        }

        if($celebrity_id){
            $answerData = $this->moduleModel->findAllByQuestionAndUserID($this->questionID, $celebrity_id);
        }else{
            $answerData = $this->moduleModel->findAllByQuestionID($this->questionID);
        }
        return $this->parseDataAll($answerData);
    }

    public function reply(){

    }

    protected function parseDataOne($item)
    {
//        $item['replytime'] = date('Y-m-d H:i:s', $item['replytime']);

        if ($item['status'] == Enum::SYSTEM_STATUS_VIOLATION) {
            $item['content'] = '此内容违规';
        } else if ($item['status'] == Enum::SYSTEM_STATUS_DELETE) {
            $item['content'] = '此内容被删除';
        }

        unset($item['status']);

        return $item;
    }
}