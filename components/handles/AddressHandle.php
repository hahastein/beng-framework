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
    public function __construct()
    {
        $this->model = new AddressARModel();
        $this->address_id = 0;
        $this->showField = array();
        $this->params = array();

        //获取需要的post数据
        $post = \Yii::$app->request->post();

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
        return $this->model->findByAddressID($this->address_id, $this->showField);
    }

    public function all(){
        return $this->model->find()->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }

    public function save(){
        $this->model->setAttributes($this->params, false);
        if($this->address_id>0){
            if(!$this->model = self::one()){
                $this->error = "数据不存在";
                return false;
            }
        }
        if($this->model->save()){
            return \Yii::$app->db->lastInsertID;
        }else{
            $this->error = "数据变更失败";
            return false;
        }
    }
}