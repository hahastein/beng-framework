<?php


namespace bengbeng\framework\user;


use bengbeng\framework\models\AddressARModel;

class AddressLogic extends UserBase
{

    public function __construct()
    {
        $this->model = new AddressARModel();
    }

    public function All(){
        return $this->model->find()->where([
            'user_id' => $this->getUserID()
        ])->orderBy([
            'is_default' => SORT_DESC
        ])->asArray()->all();
    }
}