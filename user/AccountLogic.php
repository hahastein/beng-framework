<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\components\handles\UploadHandle;
use yii\db\Exception;

/**
 * 账户系统
 * Class AccountLogic
 * @package bengbeng\framework\user
 */
class AccountLogic extends UserBase
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    public function Info(){
        return $this->userModel->findOneByUserId($this->getUserID())->toArray();
    }

    /**
     * 修改用户信息(修正中，无法使用)
     * @param array $user
     * @return bool
     */
    public function modify($user){
        $transaction = \Yii::$app->db->beginTransaction();
        try{

            $upload = new UploadHandle([
                'driverConfig' => [
                    'savePath' => 'upload/user'
                ]
            ]);

            if($upload->getFileCount() > 1){
                throw new Exception('头像不能上传多张');
            }

            $result = $upload->save(false);
            if($result === false){
                throw new Exception($upload->getError());
            }

            $user['avatar_head'] = $result[0]['path'];

            if(!$this->userModel->dataUpdate(function (ActiveOperate $operate) use ($user){
                $operate->where(['user_id' => $this->getUserID()]);
                $operate->params($user);
            })){
                throw new Exception('修改用户信息失败');
            }

            $transaction->commit();
            return true;

        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

}