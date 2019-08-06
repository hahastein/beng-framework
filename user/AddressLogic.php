<?php


namespace bengbeng\framework\user;


use bengbeng\framework\models\AddressARModel;

class AddressLogic extends UserBase
{

    private $addressID;

    public function __construct()
    {
        $this->model = new AddressARModel();
    }

    /**
     * 获取收货地址列表
     * @return array
     */
    public function All(){
        return $this->model->find()->where([
            'user_id' => $this->getUserID()
        ])->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }

    /**
     * 获取一个收货地址
     * @return array|null
     */
    public function one(){
        $this->getPost();
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
            $this->model->setScenario('modify');
            if (!$this->model) {
                $this->error = "数据不存在";
                return false;
            }
        }else{
            $this->model->setScenario('insert');
        }

        $this->model->setAttributes($this->saveParams);

        if($this->model->validate()) {
            if ($this->addressID ==0 ) {
                $this->model->addtime = time();
            }
            if(isset($this->params['is_default'])){
                $this->model->is_default = $this->saveParams['is_default'];
            }
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
            $this->addressID = $this->saveParams['address_id'];
            unset($this->saveParams['address_id']);
            $this->saveParams['user_id'] = $this->getUserID();
        }
    }
}