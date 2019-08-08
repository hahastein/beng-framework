<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\im\NIMHandle;
use bengbeng\framework\models\UserRelationARModel;
use yii\db\Exception;

class FriendLogic extends UserBase
{

    private $userRelationModel;
    private $nim;

    public function __construct()
    {
        $this->nim = new NIMHandle();
        $this->userRelationModel = new UserRelationARModel();
    }

    public function removeFriend($friendUnionID){
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $myID = $this->getUserID();
            $friendID = UserUtil::getUserIDByImID($friendUnionID);

            if(!$friendID){
                throw new Exception('没有找到相关用户');
            }

            $model = $this->userRelationModel->findRelationByTowID($myID, $friendID);
            if (!$model) {
                throw new Exception('你们不是好友，谈不上删除好友');
            }


            if ($model->delete()) {

                $result = $this->nim->friend->deleteFriend($this->getUser()->imID, $friendUnionID);
                if($result){
                    $transaction->commit();
                    return true;
                }else{
                    throw new Exception($this->nim->friend->error);
                }

            } else {
                throw new Exception('删除好友失败');
            }

        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 添加好友
     * @param $friendUnionID
     * @return bool|string
     * @throws Exception
     */
    public function addFriend($friendUnionID){

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $myID = $this->getUserID();
            $friendCache = UserUtil::getCache($friendUnionID);

            if(!$friendCache){
                throw new Exception('没有找到相关用户');
            }

            $friendID = $friendCache->userID;

            $model = $this->userRelationModel->findRelationByTowID($myID, $friendID);
            if ($model) {
                throw new Exception('您和'.$friendCache->nickname.'已经是好友了');
            }

            $this->userRelationModel->send_user_id = $myID;
            $this->userRelationModel->accept_user_id = $friendID;
            $this->userRelationModel->status = 10;
            $this->userRelationModel->createtime = time();
            $this->userRelationModel->updatetime = time();

            if ($this->userRelationModel->save()) {

                $result = $this->nim->friend->addFriend($this->getUser()->imID, $friendCache->imID);
                if($result){
                    $returnID = \Yii::$app->db->lastInsertID;
                    $transaction->commit();
                    return $returnID;
                }else{
                    throw new Exception($this->nim->friend->error);
                }

            } else {
                throw new Exception('添加好友失败');
            }

        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }
}