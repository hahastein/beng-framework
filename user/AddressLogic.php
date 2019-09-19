<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\models\AddressARModel;
use yii\db\ActiveQuery;

/**
 * 收货地址功能
 * Class AddressLogic
 * @package bengbeng\framework\user
 */
class AddressLogic extends UserBase
{

    private $addressID;

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new AddressARModel();
    }

    /**
     * 获取收货地址列表
     * @return array
     */
    public function all(){
        return $this->moduleModel->dataSet(function (ActiveQuery $query){
            $query->where([
                'user_id' => $this->getUserID()
            ])->orderBy([
                'is_default' => SORT_DESC
            ])->asArray();
        });
    }

    /**
     * 获取一个收货地址
     * @return static|null
     */
    public function one(){
//        $this->getPost();
        return $this->moduleModel->findByAddressID($this->addressID, $this->getUserID(), []);
    }

    /**
     * 获取常用地址
     * @return array|null
     */
    public function frequent(){
        return $this->moduleModel->find()->where([
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

            $defaultInfo = $this->moduleModel->dataOne(function (ActiveQuery $query){
                $query->where([
                    'user_id' => $this->getUserID(),
                    'is_default' => 1
                ]);
            });

            if($defaultInfo){
                $defaultInfo->is_default = 0;
                if(!$defaultInfo->save()){
                    throw new \Exception('修改失败');
                }
            }

            $changeCur = $this->moduleModel->dataUpdate(function (ActiveOperate $operate){
                $operate->where([
                    'user_id' => $this->getUserID(),
                    'address_id' => $this->addressID
                ]);
                $operate->params(['is_default' => 1]);
            });

            if($changeCur){
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
            $this->moduleModel = self::one();
            if(!$this->moduleModel){
                throw new \Exception('数据不存在');
            }

            if($this->moduleModel->delete()){
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
            $this->moduleModel = self::one();
            if (!$this->moduleModel) {
                $this->error = "数据不存在";
                return false;
            }
            $this->moduleModel->setScenario('modify');
        }else{
            $this->moduleModel->addtime = time();
            $this->moduleModel->setScenario('insert');
        }

//        $this->moduleModel->setAttributes($this->saveParams);

        if($this->moduleModel->load($this->saveParams, '')){
            $this->moduleModel->is_default = (int)$this->saveParams['is_default'];
            if ($this->addressID === 0) {
                $this->addressID = time();
            }
            if ($this->moduleModel->save()) {
                return $this->addressID > 0 ? $this->addressID : \Yii::$app->db->lastInsertID;
            } else {
                $this->error = "数据变更失败";
                return false;
            }
        }else{
            $this->error = current($this->moduleModel->getFirstErrors());
            return false;
        }
    }

    /**
     * 组装post数据
     */
    private function getPost(){
        if(\Yii::$app->request->isPost){
            $this->saveParams = \Yii::$app->Beng->PostData([
                'address_id','address','city','name','phone', '' => 'area_name'
            ]);

            var_dump($this->saveParams);die;
            if(isset($this->saveParams['address_id'])){
                $this->addressID = $this->saveParams['address_id'];
                unset($this->saveParams['address_id']);
            }else{
                $this->addressID = 0;
            }

            $this->saveParams['is_default'] = \Yii::$app->request->post('is_default', 0);

            $this->saveParams['user_id'] = $this->getUserID();
        }
    }
}