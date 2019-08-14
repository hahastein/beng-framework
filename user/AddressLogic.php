<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\models\AddressARModel;
use yii\db\ActiveQuery;

class AddressLogic extends UserBase
{

    private $addressID;

    public function __construct()
    {
        parent::__construct();
        $this->model = new AddressARModel();
    }

    /**
     * 获取收货地址列表
     * @return array
     */
    public function all(){
        return $this->model->find()->where([
            'user_id' => $this->getUserID()
        ])->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }

    /**
     * 获取一个收货地址
     * @return static|null
     */
    public function one(){
//        $this->getPost();
        return $this->model->findByAddressID($this->addressID, $this->getUserID(), []);
    }

    /**
     * 获取常用地址
     * @return array|null
     */
    public function frequent(){
        return $this->model->find()->where([
            'user_id' => $this->getUserID(),
            'is_default' => 1
        ])->asArray()->one();
    }

    /**
     * 设置默认地址
     * @return bool
     * @throws \yii\db\Exception
     */
    public function modifyDefault(){
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $this->getPost();
            if ($this->addressID <= 0) {
                throw new \Exception('参数错误');
            }
            $changeOrg = $this->model->dataUpdate(function (ActiveOperate $operate){
                $operate->where([
                    'user_id' => $this->getUserID(),
                ]);
                $operate->params(['is_default' => 0]);
            });

            $changeCur = $this->model->dataUpdate(function (ActiveOperate $operate){
                $operate->where([
                    'user_id' => $this->getUserID(),
                    'address_id' => $this->addressID
                ]);
                $operate->params(['is_default' => 1]);
            });

            if($changeOrg && $changeCur){
                $transaction->commit();
                return true;
            }else{
                throw new \Exception('修改失败');
            }
        }catch (\Exception $ex) {
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 删除收货地址
     * @return bool
     */
    public function delete(){
        try{
            $this->getPost();
            if ($this->addressID <= 0) {
                throw new \Exception('参数错误');
            }
            $this->model = self::one();
            if(!$this->model){
                throw new \Exception('数据不存在');
            }

            if($this->model->delete()){
                return true;
            }else{
                throw new \Exception('删除失败');
            }
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }catch (\Throwable $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 新增一个收货地址
     * @return bool|string
     */
    public function save(){
        $this->getPost();

        if ($this->addressID > 0) {
            $this->model = self::one();
            if (!$this->model) {
                $this->error = "数据不存在";
                return false;
            }
            $this->model->setScenario('modify');
        }else{
            $this->model->addtime = time();
            $this->model->setScenario('insert');
        }

//        $this->model->setAttributes($this->saveParams);


        if($this->model->load($this->saveParams)){

//            if(isset($this->params['is_default'])){
//                $this->model->is_default = $this->saveParams['is_default'];
//            }
            if ($this->model->save()) {
                return $this->addressID > 0 ? $this->addressID : \Yii::$app->db->lastInsertID;
            } else {
                $this->error = "数据变更失败";
                return false;
            }
        }else{
            $this->error = current($this->model->getFirstErrors());
            return false;
        }
    }

    /**
     * 组装post数据
     */
    private function getPost(){
        if(\Yii::$app->request->isPost){
            $this->saveParams = \Yii::$app->Beng->PostData([
                'address_id','address','city','name','phone','is_default'
            ]);
            if(isset($this->saveParams['address_id'])){
                $this->addressID = $this->saveParams['address_id'];
                unset($this->saveParams['address_id']);
            }else{
                $this->addressID = 0;
            }
            $this->saveParams['user_id'] = $this->getUserID();
        }
    }
}