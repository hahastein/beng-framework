<?php


namespace bengbeng\framework\user;


use bengbeng\framework\components\handles\im\NIMHandle;
use bengbeng\framework\models\UserGroupARModel;
use bengbeng\framework\models\UserGroupUserARModel;

class GroupLogic extends UserBase
{
    private $groupModel;
    private $groupUserModel;
    private $nim;

    public function __construct()
    {
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

                $result = $this->nim->group->createGroup($this->saveParams['name'], $this->getUser()->imID, $imIDs, '', $this->saveParams['desc']);
                if($result){
                    $transaction->commit();
                    return $returnID;
                }else{
                    throw new \Exception($this->nim->friend->error);
                }

            } else {
                throw new \Exception('创建群成功');
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