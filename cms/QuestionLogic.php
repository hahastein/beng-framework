<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\components\handles\UploadHandle;
use bengbeng\framework\models\cms\QuestionsARModel;

/**
 * 问题逻辑处理
 * Class QuestionLogic
 * @package bengbeng\framework\cms
 */
class QuestionLogic extends CmsBase
{

    public function __construct()
    {
        parent::__construct();
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

    public function post(){

    }

    public function reply($content = null){
        if(!$content){
            $content = \Yii::$app->request->post('content', '');
        }

        $upload = new UploadHandle([
            'maxSize' => 5,
            'savePath' => 'upload/answer'
        ]);

        if($result = $upload->save(false)){
            var_dump($result[0]['path']);die;
        }else{
            var_dump('上传失败');die;

        }
    }

    /**
     * 此问题是否存在
     * @param $code
     * @return bool
     */
    public function exits($code){
        return $this->moduleModel->exits($this->questionID, $code);
    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);
        //生成H5地址
        $item['h5_url'] = \Yii::getAlias('@hybridUrl') . '/faq/' . $item['url_code'];
        return $item;
    }
}