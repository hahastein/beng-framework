<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Enum;
use bengbeng\framework\models\cms\AnswersARModel;
use bengbeng\framework\models\cms\QuestionsARModel;

/**
 * 问答系统
 * Class FaqLogic
 * @package bengbeng\framework\cms
 */
class FaqLogic extends CmsBase
{

    private $questionID;
    private $answerID;
    private $answerModel;

    public function __construct()
    {
        parent::__construct();
        $this->questionID = \Yii::$app->request->post('questionid', 0);
        $this->answerID = \Yii::$app->request->post('answerid', 0);

        $this->moduleModel = new QuestionsARModel();
        $this->answerModel = new AnswersARModel();
        $this->moduleModel->showField = [
            'question_id',
            'url_code',
            'title',
            'content',
            'user_id',
            'cate_id',
            'fav_count',
            'view_count',
            'reply_count',
            'share_count',
            'is_reply',
            'show_img',
            'createtime'
        ];
    }

    public function all()
    {
        $data = $this->moduleModel->findAllByCateID();
        return $this->parseDataAll($data);
    }

    public function info($code)
    {

        $this->moduleModel->with = ['identify'];
        $data = $this->moduleModel->findInfoByQuestionID($this->questionID, $code);

        return $this->parseDataOne($data);

    }

    public function search($keyword){
        $questionData = $this->moduleModel->findAllByKeyword($keyword);
        return $this->parseDataAll($questionData);
    }

    public function answerInfo($identify_user_id = false)
    {
        if($identify_user_id){
            $answerData = $this->answerModel->findAllByQuestionAndUserID($this->questionID, $identify_user_id);

        }else{
            $answerData = $this->answerModel->findAllByQuestionID($this->questionID);
        }
        return $this->parseDataAll($answerData, 'parseAnswerDataOne');
    }

    public function answerExits($code){
        return $this->moduleModel->exits($this->questionID, $code);
    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);

        //生成H5地址
        $item['h5_url'] = \Yii::getAlias('@hybridUrl') . '/faq/' . $item['url_code'];

        return $item;
    }

    protected function parseAnswerDataOne($item)
    {
        $item = parent::parseDataOne($item);

        $item['replytime'] = date('Y-m-d H:i:s', $item['replytime']);

        if ($item['status'] == Enum::SYSTEM_STATUS_VIOLATION) {
            $item['content'] = '此内容违规';
        } else if ($item['status'] == Enum::SYSTEM_STATUS_DELETE) {
            $item['content'] = '此内容被删除';
        }

        unset($item['status']);

        return $item;
    }

}