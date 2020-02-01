<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\im\NIMHandle;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\models\UserRelationARModel;
use bengbeng\framework\models\UserTokenARModel;
use yii\db\Exception;

/**
 * 好友系统，可对IM进行设置
 * Class FriendLogic
 * @package bengbeng\framework\user
 */
class FriendLogic extends UserBase
{

    private $userRelationModel;
    private $nim;

    public function __construct()
    {
        parent::__construct();
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
        }catch (\Throwable $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 添加好友
     * @param $friendUnionID
     * @param $mode
     * @return bool|string
     * @throws Exception
     */
    public function addFriend($friendUnionID, $mode = ''){

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $myID = $this->getUserID();

            $friendID = 0;
            $im_id = '';
            $friend_nickname = '';
            if($mode == 'im'){

                $friendToken = UserTokenARModel::findOne(['unionid' => $friendUnionID]);

                if($friendToken){
                    $friendCache = UserARModel::find()->where(['user_id' => $friendToken->user_id])->one();
                    $friendID = $friendCache->user_id;
                    $friend_nickname = $friendCache->nickname;
                    $im_id = $friendUnionID;
                }
            }else{
                $friendCache = UserUtil::resetCache($friendUnionID);
                if($friendCache){
                    $friendID = $friendCache->userID;
                    $friend_nickname = $friendCache->nickname;
                    $im_id = $friendCache->imID;
                }

                var_dump($friendCache);die;

            }


            if($friendID <= 0){
                throw new Exception('没有找到相关用户');
            }


            $model = $this->userRelationModel->findRelationByTowID($myID, $friendID);
            if ($model) {
                throw new Exception('您和'.$friend_nickname.'已经是好友了');
            }

            $this->userRelationModel->send_user_id = $myID;
            $this->userRelationModel->accept_user_id = $friendID;
            $this->userRelationModel->status = 10;
            $this->userRelationModel->createtime = time();
            $this->userRelationModel->updatetime = time();

            if ($this->userRelationModel->save()) {

                $result = $this->nim->friend->addFriend($this->getUser()->imID, $im_id);
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

    public function getFriends(){

    }

    public function friendCount(){
        return UserRelationARModel::find()->where(['send_user_id' => $this->getUserID()])->orWhere(['accept_user_id' => $this->getUserID()])->count();
    }
}