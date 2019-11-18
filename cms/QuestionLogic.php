<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\UploadHandle;
use bengbeng\framework\components\helpers\StringHelpers;
use bengbeng\framework\models\cms\AnswersARModel;
use bengbeng\framework\models\cms\QuestionsARModel;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\system\System;
use bengbeng\framework\models\cms\FaqIdentifyARModel;
use bengbeng\framework\user\User;
use bengbeng\framework\user\UserUtil;
use yii\db\ActiveQuery;
use yii\db\Exception;

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
            'nickname',
            'avatar_head',
            'cate_id',
            'cate_name',
            'fav_count',
            'view_count',
            'reply_count',
            'share_count',
            'is_reply',
            'status',
            'show_img',
            'createtime'
        ];
    }

    public function all()
    {
        $data = $this->moduleModel->findAllByCateID();
        return $this->parseDataAll($data);
    }

    public function noReply(){
        $data = $this->moduleModel->findAllByNoReply();
        return $this->parseDataAll($data);
    }

    public function info($code)
    {

        $this->moduleModel->with = ['identify.user'];
        if ($this->getUserID()) {
            $this->moduleModel->with['fav'] = function (ActiveQuery $query) {
                $query->where([
                    'module' => Enum::MODULE_TYPE_FAQS,
                    'user_id' => $this->getUserID()
                ]);
            };
//            $isImage = true;
        }

        $data = $this->moduleModel->findInfoByQuestionIDAndCode($this->questionID, $code);
//        var_dump($this->getUser());die;

//        $userInfo = new

//        var_dump($this->getUser());die;
        if(!$this->getUser()->isAuth && $this->getUserID() != $data['user_id']){
            unset($data['images']);
        }

        $user_id = $data['user_id'];

        if(isset($data['identify'])) {
            foreach ($data['identify'] as $key => $identify) {
                if ($identify['user']) {
                    $auth_info = json_decode($identify['user']['auth_info'], true);
                    $identify['doctor_name'] = $auth_info['doctorname'];
                    unset($identify['user']);
                    $data['identify'][$key] = $identify;
                }
                unset($data['identify'][$key]['user_id'], $data['identify'][$key]['question_id']);
            }
        }
        $data = $this->parseDataOne($data);
        $data['user_id'] = $user_id;
        return $data;

    }

    public function my()
    {

        if($this->getUserID()){
            $data = $this->moduleModel->findAllByUserID($this->getUserID());
            return $this->parseDataAll($data);
        }else{
            return false;
        }

    }

    public function celebrityReply(){

    }


    public function search($keyword){
        $questionData = $this->moduleModel->findAllByKeyword($keyword);
        return $this->parseDataAll($questionData);
    }

    public function postOnlyExtend($extend = []){
        return $this->post(null, null, $extend);
    }


    public function post($title = null, $content = null, $extend = []){
        if(!$content){
            $content = \Yii::$app->request->post('content', '');
        }

        if(!$title){
            $title = \Yii::$app->request->post('title', '');
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try{

            if(empty($content) || strlen($content)<5){
                throw new Exception('回复失败，内容长度不够');
            }

            if(empty($title)){
                if(strlen($content)>20){
                    $title = mb_substr($content,0,20);
                }else{
                    $title = $content;
                }
            }

            $this->moduleModel->url_code = 'Q_'.StringHelpers::genRandomString(12);

            $this->moduleModel->title = $title;
            $this->moduleModel->content = $content;
            $this->moduleModel->user_id = $this->getUserID();
            $this->moduleModel->createtime = $this->moduleModel->updatetime = time();

            foreach ($extend as $key => $item){
                $this->moduleModel->$key = $item;
            }

            if(!$this->moduleModel->save()){
                throw new Exception('发布失败');
            }


            $upload = new UploadHandle([
                'maxSize' => 5,
                'driverConfig'=>[
                    'savePath' => 'upload/question'
                ]
            ]);

            if($upload->getFileCount() > 5){
                throw new Exception('图片不能超过5张哦');
            }else{
                $uploadResult = $upload->save(false);

                $system = new System();
                //写入图片
                if($uploadResult && !$system->attachment->save($uploadResult, \Yii::$app->db->lastInsertID, Enum::MODULE_TYPE_FAQS)){
                    throw new Exception('回复失败[20081]。图片写入失败');
                }
            }

            $transaction->commit();
            return true;

        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }

    }

    /**
     * 回复数据
     * @param string|null $content
     * @return bool
     */
    public function reply($content = null){
        if(!$content){
            $content = \Yii::$app->request->post('content', '');
        }

        $c_unionid = \Yii::$app->request->post('c_unionid', '');


        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $this->moduleModel->showField = '';
            if(!$questionModel = $this->moduleModel->findInfoByQuestionID($this->questionID)){
                throw new Exception('问题不存在或者已经已经关闭');
            }

            if(empty($content) || strlen($content)<5){
                throw new Exception('回复失败，内容长度不够');
            }

            //组ID
            $groupID = 0;
            //是否是认证用户
            $isIdentify = false;

            //是否是自己的贴子
            if($this->getUserID() == $questionModel->user_id){
                if(!empty($c_unionid) && $cUnionCacheInfo = UserUtil::getCache($c_unionid)){

                    $groupID = $cUnionCacheInfo->userID?$cUnionCacheInfo->userID:0;

                }

//                if(!empty($c_unionid) && $identify_user_id){
//                    $groupID = $identify_user_id;
//                }
            }else{

                $userModel = new UserARModel();
                $userInfo = $userModel->findOneByUserId($this->getUserID());
                if($c_unionid == $userInfo['unionid']) {
                    if ($userInfo['auth_type'] == 21 && !empty($userInfo['auth_info'])) {
                        $isIdentify = true;

                        $groupID = $this->getUserID();

                        $faqIdentify = new FaqIdentifyARModel();
                        //将追问的内容挂入到医生组下
                        if ($questionModel->status == 20) {

                            AnswersARModel::updateAll(
                                ['group_id' => $groupID],
                                ['question_id' => $this->questionID]
                            );
                        }

                        //写入问答的认证表

                        if (!$faqIdentify::find()->where([
                            'question_id' => $this->questionID,
                            'user_id' => $this->getUserID(),
                            'unionid' => $userInfo['unionid']
                        ])->exists()) {
                            $faqIdentify->question_id = $this->questionID;
                            $faqIdentify->user_id = $this->getUserID();
                            $faqIdentify->unionid = $userInfo['unionid'];

                            if (!$faqIdentify->save()) {
                                throw new Exception('回复失败[20080]。创建认证用户关联失败');
                            }
                        }

                    }
                }

            }

            //写入回复数据
            $answerModel = new AnswersARModel();
            $answerModel->question_id = $this->questionID;
            $answerModel->content = $content;
            if($isIdentify){
                $answerModel->is_identify = 1;
            }
            $answerModel->group_id = $groupID;

            $answerModel->user_id = $this->getUserID();
            $answerModel->status = 10;
            $answerModel->replytime = time();

            if(!$answerModel->save()){
                throw new Exception('回复失败[20080]。回答写入失败');
            }

            //更新问题表的回复总数及最后回复时间

            $questionModel->reply_count = $questionModel->reply_count+1;
            $questionModel->updatetime = $questionModel->replytime = time();
            if($isIdentify && $questionModel->status == 20){
                $questionModel->status = 10;
            }

            if(!$questionModel->save()){
                throw new Exception('回复失败[20082]。更新主表失败');
            }

            $upload = new UploadHandle([
                'maxSize' => 5,
                'driverConfig'=>[
                    'savePath' => 'upload/answer'
                ]
            ]);

            if($upload->getFileCount() > 1){
                throw new Exception('回复的图片只能上传1张');
            }else{
                $uploadResult = $upload->save(false);

                //写入图片
                if($uploadResult && !(new System())->attachment->save($uploadResult, \Yii::$app->db->lastInsertID, Enum::MODULE_TYPE_FAQS_REPLAY)){
                    throw new Exception('回复失败[20081]。图片写入失败');
                }
            }

            $transaction->commit();
            return true;
//            var_dump($result[0]['originPath']);die;



        }catch (Exception $ex){

            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 此问题是否存在
     * @param $code
     * @return bool
     */
    public function exists($code){
        return $this->moduleModel->exists($this->questionID, $code);
    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);
        //生成H5地址
        $item['h5_url'] = \Yii::getAlias('@hybridUrl') . '/faq/' . $item['url_code'];
        if(isset($item['fav'])){
            $item['fav'] = 1;
        }else{
            $item['fav'] = 0;
        }
        if(!isset($item['user']) || !$item['user']){
            $item['user']['nickname'] = $item['nickname'];
            $item['user']['avatar_head'] = $item['avatar_head'];
        }

        return $item;
    }
}