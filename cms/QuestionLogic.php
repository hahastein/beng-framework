<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\StructureHandle;
use bengbeng\framework\components\handles\UploadHandle;
use bengbeng\framework\components\helpers\StringHelpers;
use bengbeng\framework\models\AttachmentARModel;
use bengbeng\framework\models\CategoryARModel;
use bengbeng\framework\models\cms\AnswersARModel;
use bengbeng\framework\models\cms\CelebrityARModel;
use bengbeng\framework\models\cms\QuestionsARModel;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\system\System;
use bengbeng\framework\models\cms\FaqIdentifyARModel;
use bengbeng\framework\user\User;
use bengbeng\framework\user\UserUtil;
use yii\db\ActiveQuery;
use yii\db\Exception;
use function GuzzleHttp\Psr7\str;

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
            'celebrity_id',
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

//        var_dump($this->getUser());die;

        //转换cateID

        if ($this->getUser() && $this->getUser()->tags) {
            $relationID = CategoryARModel::find()->select('cate_id')->where(['in', 'relation_cateid', $this->getUser()->tags])->andWhere(['module' => 20])->asArray()->all();
            $relationID = array_flip(array_flip(array_column($relationID, 'cate_id')));
            $this->moduleModel->showField = [
                'question_id',
                'url_code',
                'title',
                'user_id',
                'nickname',
                'avatar_head',
                'cate_id',
                'cate_name',
                'view_count',
                'is_reply',
                'status',
                'show_img',
                'createtime'
            ];
            $data = $this->moduleModel->findAllByTags($relationID);
        } else {
            $data = $this->moduleModel->findAllByCateID();

        }

        return $this->parseDataAll($data);
    }

    public function noReply($field, $image_type = 'more', $level_id = 0)
    {
        if (!empty($field)) {
            $this->moduleModel->showField = $field;
        }

        if ($image_type == 'one') {
            $this->moduleModel->with = ['image'];
        } else if ($image_type == 'more') {
            $this->moduleModel->with = ['images'];
        }
        $this->moduleModel->with['lastAnswer'] = function (ActiveQuery $query) {
            $query->select(['answer_id', 'question_id', 'content']);
        };

        $data = $this->moduleModel->findAllByNoReply(0, $level_id);
        return $this->parseDataAll($data);
    }

    public function info($code=''){

        //关联是否收藏
        if ($this->getUserID()) {
            $this->moduleModel->with['fav'] = function (ActiveQuery $query) {
                $query->where([
                    'module' => Enum::MODULE_TYPE_FAQS,
                    'user_id' => $this->getUserID()
                ]);
            };
//            $isImage = true;
        }

        $this->moduleModel->showField = [
            'question_id',
            'url_code',
            'content',
            'user_id',
            'nickname',
            'avatar_head',
            'cate_id',
            'cate_name',
            'is_reply',
            'celebrity_id',
            'celebrity_name',
            'createtime'
        ];

        $data = $this->moduleModel->findInfoByQuestionIDAndCode($this->questionID, $code, [10, 20]);

//        var_dump($data);die;
        //转换收藏
        if(isset($data['fav'])){
            $data['fav'] = 1;
        }else{
            $data['fav'] = 0;
        }
//        var_dump($this->questionID);die;
        //判断是否有权限查看图片
        if($user = $this->getUser()){

            if($user->isAuth || $this->getUserID() == $data['user_id']){
                if($att = AttachmentARModel::find()->select('obj_url, width, height')->where(['att_type' => Enum::MODULE_TYPE_FAQS, 'object_id' => $data['question_id']])->asArray()->all()){
                    $data['images'] = $att;
                }
            }

        }

        //尝试获取V3版之前的医生信息
        $celebrityField = ['celebrity_id', 'celebrity_name', 'head', 'company', 'certificate', 'department'];
        if($data['celebrity_id'] > 0){
            $celebrity = CelebrityARModel::find()->select($celebrityField)->where(['celebrity_id' => $data['celebrity_id']])->asArray()->one();
        }else{
            $celebrity = FaqIdentifyARModel::find()->with(['celebrity' => function(ActiveQuery $query) use($celebrityField){
                $query->select($celebrityField);
            }])->where(['question_id' => $data['question_id']])->asArray()->one();

            if($celebrity && isset($celebrity['celebrity'])){
                $celebrity = $celebrity['celebrity'];
                $data['celebrity_id'] = $celebrity['celebrity_id'];
            }
        }
        //拼接医生信息
        if($celebrity){
            $data['celebrity'] = $celebrity;
            //处理医生
            StructureHandle::CelebrityInfo($data);
        }

        //处理用户
//        StructureHandle::NicknameAndAvatar($data);
        return $data;
    }

    public function infoOld($code)
    {

        $this->moduleModel->with = ['images', 'identify.user'];
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
        if (!$this->getUser() || (!$this->getUser()->isAuth && $this->getUserID() != $data['user_id'])) {
            unset($data['images']);
        }

        $user_id = $data['user_id'];

        if (isset($data['identify'])) {
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

        if ($this->getUserID()) {
            $data = $this->moduleModel->findAllByUserID($this->getUserID());
            return $this->parseDataAll($data);
        } else {
            return false;
        }

    }

    public function celebrityReply()
    {

    }


    public function search($keyword)
    {
        $questionData = $this->moduleModel->findAllByKeyword($keyword);
        return $this->parseDataAll($questionData);
    }

    public function postOnlyExtend($extend = [])
    {
        return $this->post(null, null, $extend);
    }


    public function post($title = null, $content = null, $extend = [])
    {
        if (!$content) {
            $content = \Yii::$app->request->post('content', '');
        }

        if (!$title) {
            $title = \Yii::$app->request->post('title', '');
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {

            if (empty($content) || strlen($content) < 5) {
                throw new Exception('回复失败，内容长度不够');
            }

            if (empty($title)) {
                if (strlen($content) > 20) {
                    $title = mb_substr($content, 0, 20);
                } else {
                    $title = $content;
                }
            }

            $this->moduleModel->url_code = 'Q_' . StringHelpers::genRandomString(12);

            $this->moduleModel->title = $title;
            $this->moduleModel->content = $content;
            $this->moduleModel->user_id = $this->getUserID();
            $this->moduleModel->createtime = $this->moduleModel->updatetime = time();

            foreach ($extend as $key => $item) {
                $this->moduleModel->$key = $item;
            }

            if (!$this->moduleModel->save()) {
                throw new Exception('发布失败');
            }


            $upload = new UploadHandle([
                'maxSize' => 5,
                'driverConfig' => [
                    'savePath' => 'upload/question'
                ]
            ]);

            if ($upload->getFileCount() > 5) {
                throw new Exception('图片不能超过5张哦');
            } else {
                $uploadResult = $upload->save(false);

                $system = new System();
                //写入图片
                if ($uploadResult && !$system->attachment->save($uploadResult, \Yii::$app->db->lastInsertID, Enum::MODULE_TYPE_FAQS)) {
                    throw new Exception('回复失败[20081]。图片写入失败');
                }
            }

            $transaction->commit();
            return true;

        } catch (Exception $ex) {
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }

    }

    public function singleReply(\Closure $call_back = null){

        //0文字10语音20图片
        $mode = \Yii::$app->request->post('content_mode', 0);
        $identity = \Yii::$app->request->post('identity', 0);

        $transaction = \Yii::$app->db->beginTransaction();
        try {

            if (!$questionModel = $this->moduleModel->findInfoByQuestionID($this->questionID)) {
                throw new Exception('问题不存在或者已经关闭');
            }

            $group_id = 0;
//            var_dump($this->getUser());die;
            $is_doctor = $this->getUser()->isAuth;
            $is_identity = 0;
            $celebrity_name = '';

            if($identity){
                //获取医生的DoctorID
                if($is_doctor){
                    $celebrity = CelebrityARModel::findOne(['user_id' => $this->getUserID()]);
                    if($questionModel->celebrity_id != $celebrity->celebrity_id){
                        throw new Exception('此问题已被其他医生抢答');
                    }
                    $group_id = $this->getUserID();
                    $is_identity = 1;
                    $celebrity_name = $celebrity->celebrity_name;
                }else {
                    if($questionModel->user_id != $this->getUserID()){
                        throw new Exception('您不是发起者，不能回复此咨询');
                    }
                    $celebrity = CelebrityARModel::findOne(['celebrity_id' => $questionModel->celebrity_id]);
                    $group_id = $celebrity['user_id'];
                    $is_identity = 0;
                    $celebrity_name = $celebrity->celebrity_name;
                }


            }

            $answerModel = new AnswersARModel();
            $answerModel->question_id = $this->questionID;
//            if ($isIdentify) {
//                $answerModel->is_identify = 1;
//            }

            $content = '';

            if($mode == 10 || $mode == 20){

                $content = '媒体文件';
                $upload = new UploadHandle([
                    'maxSize' => 1,
//                    'exts' => ['aac'],
                    'driverConfig' => [
                        'savePath' => 'upload/answer'
                    ]
                ]);

                if ($upload->getFileCount() > 1) {
                    throw new Exception('回复的图片只能上传1张');
                } else {
                    $uploadResult = $upload->save(false);

                    if($uploadResult){

                        $answerModel->file_path = \Yii::getAlias('@resUrl').$uploadResult[0]['originPath'];

                        if($mode == 10){
                            $audio_time = \Yii::$app->request->post('audio_time', 0);

                            $answerModel->file_att = [
                                'audio_time' => $audio_time
                            ];
                        }

                    }else{
                        throw new Exception($upload->getError());
                    }

                }
                $answerModel->content = '';

            }else{
                $content = \Yii::$app->request->post('content', '');
                if(empty($content) || strlen($content) <= 4){
                    throw new Exception('内容不能为空不能小于4个汉字');

                }
                $answerModel->content = $content;
            }

//            if ($identity) {
                $answerModel->is_identify = $is_identity;
//            }

            $answerModel->group_id = $group_id;

            if(in_array($mode, [0,10,20])){
                $answerModel->answer_type = $mode;

            }

            $answerModel->user_id = $this->getUserID();
            $answerModel->status = 10;
            $answerModel->replytime = time();

            if (!$answerModel->save()) {
                throw new Exception('回复失败[20080]。回答写入失败');
            }

            $answer_id = \Yii::$app->db->lastInsertID;

            //更新问题表的回复总数及最后回复时间

            $questionModel->reply_count = $questionModel->reply_count + 1;
            $questionModel->updatetime = $questionModel->replytime = time();
            if ($identity && $questionModel->status == 20) {
                $questionModel->status = 10;
                $questionModel->celebrity_replytime = time();

            }

            if (!$questionModel->save()) {
                throw new Exception('回复失败[20082]。更新主表失败');
            }

            //回调处理
            if($call_back){
                call_user_func($call_back, [
                    'question_id' => $questionModel->question_id,
                    'title' => $questionModel->title,
                    'post_user_id' => $questionModel->user_id,
                    'group_id' => $group_id
                ],[
                    'answer_id' => $answer_id,
                    'last_reply' => $content,
                    'last_reply_time' => time(),
                    'reply_user_id' => $this->getUserID(),
                    'reply_user_nickname' => $this->getUser()->nickname,
                    'reply_auth_name' => $celebrity_name,
                    'group_id' => $group_id
                ],$identity);
            }

            $transaction->commit();
            return true;


        }catch (\Exception $ex){
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
    public function reply($content = null)
    {
        $mode = \Yii::$app->request->post('mode', 0);

        if (!$content) {
            $content = \Yii::$app->request->post('content', '');
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->moduleModel->showField = '';
            if (!$questionModel = $this->moduleModel->findInfoByQuestionID($this->questionID)) {
                throw new Exception('问题不存在或者已经已经关闭');
            }

            if ($mode == 0 && (empty($content) || strlen($content) < 5)) {
                throw new Exception('回复失败，内容长度不够');
            }

            //组ID
            $groupID = 0;
            //是否是认证用户
            $isIdentify = false;

            //是否是自己的贴子
            if ($this->getUserID() == $questionModel->user_id) {

            } else {

            }

            //写入回复数据
            $answerModel = new AnswersARModel();
            $answerModel->question_id = $this->questionID;
            $answerModel->content = $content;
            if ($isIdentify) {
                $answerModel->is_identify = 1;
            }
            $answerModel->group_id = $groupID;

            $answerModel->user_id = $this->getUserID();
            $answerModel->status = 10;
            $answerModel->replytime = time();

            if (!$answerModel->save()) {
                throw new Exception('回复失败[20080]。回答写入失败');
            }

            $answer_id = \Yii::$app->db->lastInsertID;

            //更新问题表的回复总数及最后回复时间

            $questionModel->reply_count = $questionModel->reply_count + 1;
            $questionModel->updatetime = $questionModel->replytime = time();
            if ($isIdentify && $questionModel->status == 20) {
                $questionModel->status = 10;
            }

            if (!$questionModel->save()) {
                throw new Exception('回复失败[20082]。更新主表失败');
            }

            $upload = new UploadHandle([
                'maxSize' => 1,
                'driverConfig' => [
                    'savePath' => 'upload/answer'
                ]
            ]);

            if ($upload->getFileCount() > 1) {
                throw new Exception('回复的图片只能上传1张');
            } else {
                $uploadResult = $upload->save(false);

                //写入图片
                if ($uploadResult && !(new System())->attachment->save($uploadResult, $answer_id, Enum::MODULE_TYPE_FAQS_REPLAY)) {
                    throw new Exception('回复失败[20081]。图片写入失败');
                }
            }

            $transaction->commit();
            return true;
//            var_dump($result[0]['originPath']);die;


        } catch (Exception $ex) {

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
    public function exists($code)
    {
        return $this->moduleModel->exists($this->questionID, $code);
    }

    protected function parseDataOne($item)
    {
        $item = parent::parseDataOne($item);
        //如果url_code存在则生成H5地址
        if (isset($item['url_code'])) {
            $item['h5_url'] = \Yii::getAlias('@hybridUrl') . '/faq/' . $item['url_code'];
            unset($item['url_code']);
        }
        if (isset($item['fav'])) {
            $item['fav'] = 1;
        } else {
            $item['fav'] = 0;
        }

        StructureHandle::NicknameAndAvatar($item);
        unset($item['nickname'], $item['avatar_head']);

        if (isset($item['image'])) {
            StructureHandle::Image($item['image'], 'one');
        } else {
            unset($item['image']);
        }

        if (isset($item['images'])) {
            StructureHandle::Image($item['images'], 'more');
        } else {
            unset($item['images']);
        }

        if(isset($item['lastAnswer'])){
            $item['lastAnswer'] = $item['lastAnswer']['content'];
        }else{
            unset($item['lastAnswer']);
        }

//        $item['level'] = '主任医师';
        if(isset($item['level_name'])){
            $item['level'] = $item['level_name'];
            unset($item['level_name']);
        }

        return $item;
    }
}