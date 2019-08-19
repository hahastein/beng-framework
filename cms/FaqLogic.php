<?php


namespace bengbeng\framework\cms;

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

    public function __construct()
    {
        parent::__construct();
        $this->questionID = \Yii::$app->request->post('questionid', 0);
        $this->answerID = \Yii::$app->request->post('answerid', 0);

        $this->moduleModel = new QuestionsARModel();
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

    public function all(){
        $data = $this->moduleModel->findAllByCateID();
        return $this->parseDataAll($data);
    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);


        //生成H5地址
        $item['h5_url'] = \Yii::getAlias('@hybridUrl').'/faq/'.$item['url_code'];

        return $item;
    }

}