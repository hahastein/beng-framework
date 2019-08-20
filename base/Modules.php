<?php


namespace bengbeng\framework\base;


use bengbeng\framework\components\helpers\UrlHelper;

class Modules
{
    /**
     * @var int $cateID 分类ID
     */
    protected $cateID;
    protected $questionID;
    protected $answerID;
    protected $articleID;

    protected $moduleModel;

    public function __construct()
    {
        if(\Yii::$app->request->isPost){
            $this->cateID = \Yii::$app->request->post('cate_id', 0);
            $this->questionID = \Yii::$app->request->post('questionid', 0);
            $this->answerID = \Yii::$app->request->post('answerid', 0);
            $this->articleID = \Yii::$app->request->post('articleid', 0);
        }

        $this->init();
    }

    protected function init(){

    }

    /**
     * @param int $cateID
     */
    public function setCateID($cateID)
    {
        $this->cateID = $cateID;
    }
    /**
     * @param int $questionID
     */
    public function setQuestionID($questionID)
    {
        $this->questionID = $questionID;
    }
    /**
     * @param int $answerID
     */
    public function setAnswerID($answerID)
    {
        $this->answerID = $answerID;
    }

    protected function parseDataAll($data, $callbak = false){
        foreach ($data as $key => $item){
            $data[$key] = $callbak?$this->$callbak($item):$this->parseDataOne($item);
        }
        return $data;
    }

    protected function parseDataOne($item){
        if(isset($item['createtime'])) {
            $item['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
        }
        if(isset($item['updatetime'])) {
            $item['updatetime'] = date('Y-m-d H:i:s', $item['updatetime']);
        }

        if(isset($item['user_id'])){
            unset($item['user_id']);
        }

        if(isset($item['user']['user_id'])){
            unset($item['user']['user_id']);
        }
        return $item;
    }
}