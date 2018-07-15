<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 19:00
 */

namespace bengbeng\framework\components\handles;


use bengbeng\framework\models\AddressARModel;

class AddressHandle
{
    private $model;
    private $address_id;
    private $showField;
    private $params;
    private $error;
    private $user_id;
    public function __construct()
    {
        $this->model = new AddressARModel();
        $this->address_id = 0;
        $this->showField = array();
        $this->params = array();
        $this->user_id = 0;

        //获取需要的post数据
        $post = \Yii::$app->request->post();

        if(isset($post['address_id'])){
            $this->address_id = $post['address_id'];
        }

        if(!empty($post['user_id'])){
            $this->user_id = $post['user_id'];
            $this->params['user_id'] = $this->user_id;
        }

        if(!empty($post['address'])){
            $this->params['address'] = $post['address'];
        }

        if(!empty($post['city'])){
            $this->params['city'] = $post['city'];
        }

        if(!empty($post['name'])){
            $this->params['name'] = $post['name'];
        }

        if(!empty($post['phone'])){
            $this->params['phone'] = $post['phone'];
        }

        if(isset($post['is_default']) && trim($post['is_default']) != "") {
            $this->params['is_default'] = $post['is_default'];
        }
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        $this->params['user_id'] = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param array $showField
     */
    public function setShowField($showField)
    {
        $this->showField = $showField;
    }

    /**
     * @param int $address_id
     */
    public function setAddressId($address_id)
    {
        $this->address_id = $address_id;
    }

    public function one(){
        return $this->model->findByAddressID($this->address_id, $this->user_id, $this->showField);
    }

    public function getDefault(){
        return $this->model->find()->where([
            'user_id' => $this->user_id,
            'is_default' => 1
        ])->asArray()->all();
    }

    public function all(){
        return $this->model->find()->where([
            'user_id' => $this->user_id
        ])->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }

    public function delete(){
        try{
            if ($this->address_id <= 0) {
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

    public function save(){
        if ($this->address_id > 0) {
            $this->model = self::one();
            $this->model->setScenario('modify');
            if (!$this->model) {
                $this->error = "数据不存在";
                return false;
            }
        }else{
            $this->model->setScenario('insert');
        }

        $this->model->setAttributes($this->params);

        if($this->model->validate()) {
            if ($this->address_id ==0 ) {
                $this->model->addtime = time();
            }
            if(isset($this->params['is_default'])){
                $this->model->is_default = $this->params['is_default'];
            }
            if ($this->model->save()) {
                return $this->address_id > 0 ? $this->address_id : \Yii::$app->db->lastInsertID;
            } else {
                $this->error = "数据变更失败";
                return false;
            }
        }else{
            $this->error = current($this->model->getFirstErrors());
            return false;
        }
    }
}