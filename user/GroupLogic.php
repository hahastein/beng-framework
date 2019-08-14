<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\components\handles\im\NIMHandle;
use bengbeng\framework\models\UserGroupARModel;
use bengbeng\framework\models\UserGroupUserARModel;

/**
 * 群系统
 * Class GroupLogic
 * @package bengbeng\framework\user
 */
class GroupLogic extends UserBase
{
    private $groupModel;
    private $groupUserModel;
    private $nim;

    const REMOVE_MODE_GROUPID = 10;
    const REMOVE_MODE_IMID = 20;

    public function __construct()
    {
        parent::__construct();
        $this->nim = new NIMHandle();
        $this->groupModel = new UserGroupARModel();
        $this->groupUserModel = new UserGroupUserARModel();

    }

    public function createGroup(){
        $this->getPost();
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $myID = $this->getUserID();

            $model = $this->groupModel->findInfoByName($this->saveParams['name']);
            if ($model) {
                throw new \Exception('群名字已经存在');
            }

            $this->groupModel->create_user_id = $myID;
            $this->groupModel->group_name = $this->saveParams['name'];
            $this->groupModel->group_desc = $this->saveParams['desc'];
            $this->groupModel->createtime = time();
            $this->groupModel->updatetime = time();

            if ($this->groupModel->save()) {
                $returnID = \Yii::$app->db->lastInsertID;

                //解析加入的ID
                $imIDs = explode(',',$this->saveParams['ids']);

//                $key = ['group_id', 'user_id'];
//                $insertValues = [];
//                foreach ($imIDs as $imID){
//                    $insertValues[] = [$returnID, $imID];
//                }
//                $result = UserGroupUserARModel::find()->createCommand()->batchInsert(UserGroupUserARModel::tableName(), $key, $insertValues)->execute();

                $result = $this->nim->group->createGroup($this->saveParams['name'], $this->getUser()->imID, $imIDs, '', $this->saveParams['desc'], '欢迎加入我们的群');
                if($result){

                    if(!isset($this->nim->group->returnData['tid'])){
                        throw new \Exception('群创建失败，没有获取到ID');
                    }

                    if(!$this->groupModel->dataUpdate(function (ActiveOperate $operate) use ($returnID){
                        $operate->where(['group_id' => $returnID]);
                        $operate->params([
                            'im_group_id' => $this->nim->group->returnData['tid']
                        ]);
                    })){
                        throw new \Exception('群创建失败，ID更新失败');
                    }
                    $transaction->commit();
                    return $returnID;
                }else{
                    throw new \Exception($this->nim->group->error);
                }

            } else {
                throw new \Exception('创建群失败');
            }

        }catch (\Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function removeByImID($groupID){
        return self::remove($groupID, self::REMOVE_MODE_IMID);
    }

    public function remove($groupID, $mode = self::REMOVE_MODE_GROUPID){
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $myID = $this->getUserID();
            if($mode == self::REMOVE_MODE_IMID){
                $model = $this->groupModel->findInfoByIMID($groupID, $myID);
            }else{
                $model = $this->groupModel->findInfoByGroupID($groupID, $myID);
            }
            if (!$model) {
                throw new \Exception('群不存在');
            }

            if ($model->delete()) {
                $result = $this->nim->group->removeGroup($model->im_group_id, $this->getUser()->imID);
                if($result){
                    $transaction->commit();
                    return true;
                }else{
                    throw new \Exception($this->nim->group->error);
                }

            } else {
                throw new \Exception('删除群失败');
            }

        }catch (\Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 组装post数据
     */
    private function getPost(){
        if(\Yii::$app->request->isPost){
            $this->saveParams = \Yii::$app->Beng->PostData([
                'name','desc','ids'
            ]);
        }
    }
}