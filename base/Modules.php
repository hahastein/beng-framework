<?php


namespace bengbeng\framework\base;

use bengbeng\framework\user\UserProperty;
use bengbeng\framework\user\UserUtil;
use yii\db\Exception;

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

    private $userID;
    private $unionID;

    /**
     * @var UserProperty $user
     */
    protected $user;

    /**
     * @var string $error
     */
    protected $error;

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

    /**
     * @param mixed $userID
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return UserProperty
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserProperty $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $unionID
     */
    public function setUnionID($unionID)
    {
        $this->unionID = $unionID;

        if(!$this->userID){
            //如果没有userid，需要将unionid转换为userid
            $this->user = $this->unionToUser();
            if($this->user){
                $this->userID = $this->user->userID;
            }
        }
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

    /**
     * 获取用户后转换为OBJ
     * @return UserProperty|bool|NULL
     */
    private function unionToUser(){
        var_dump($this->unionID);die;
        $userProperty = UserUtil::getCache($this->unionID);
        if($userProperty && isset($userProperty->userID)){
            return $userProperty;
        }
        return false;
    }

    protected function Transaction(){
        $tr = \Yii::$app->db->beginTransaction();
        try{
            $this->TransactionLogic();
            $tr->commit();
            return true;
        }catch (Exception $ex) {
            $tr->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    protected function TransactionLogic(){

    }
}