<?php


namespace bengbeng\framework\user;


use bengbeng\framework\components\handles\im\NIMHandle;
use bengbeng\framework\models\UserRelationARModel;
use yii\db\Exception;

class FriendLogic extends UserBase
{

    private $userRelationModel;
    private $nim;

    public $error;

    public function __construct()
    {
        $this->nim = new NIMHandle();
        $this->userRelationModel = new UserRelationARModel();
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
            $userCache = UserUtil::getCache($friendUnionID);
            $friendID = $userCache->userID;

            $model = $this->userRelationModel->findRelationByTowID($myID, $friendID);
            if ($model) {
                throw new Exception('您和'.$userCache->nickname.'已经是好友了');
            }

            $model->send_user_id = $myID;
            $model->accept_user_id = $friendID;
            $model->status = 10;
            $model->createtime = time();
            $model->updatetime = time();

            if ($model->save()) {

                $result = $this->nim->friend->addFriend($myID, $friendID);
                if($result){
                    $transaction->commit();
                    return \Yii::$app->db->lastInsertID;
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