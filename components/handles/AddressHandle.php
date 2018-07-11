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

        if(isset($post['user_id'])){
            $this->user_id = $post['user_id'];
        }

        if(isset($post['address'])){
            $params['address'] = $post['address'];
        }

        if(isset($post['city'])){
            $params['city'] = $post['city'];
        }

        if(isset($post['name'])){
            $params['name'] = $post['name'];
        }

        if(isset($post['phone'])){
            $params['phone'] = $post['phone'];
        }

        if(isset($post['is_default'])){
            $params['is_default'] = $post['is_default'];
        }else{
            $params['is_default'] = 0;
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

    public function all(){
        return $this->model->find()->where([
            'user_id' => $this->user_id
        ])->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }

    public function save(){
        if ($this->address_id > 0) {
            $this->model->setScenario('modify');
        }else{
            $this->model->setScenario('insert');
        }
        $this->model->setAttributes($this->params);
        if($this->model->validate()) {
            if ($this->address_id > 0) {
                if (!$this->model = self::one()) {
                    $this->error = "数据不存在";
                    return false;
                }
            } else {
                $this->model->addtime = time();
                $this->model->user_id = $this->user_id;
            }
            $this->model->is_default = $this->params['is_default'];
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