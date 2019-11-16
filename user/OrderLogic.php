<?php


namespace bengbeng\framework\user;

use bengbeng\framework\models\order\OrdersARModel;
use yii\db\ActiveQuery;

class OrderLogic extends UserBase
{

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new OrdersARModel();
    }

    /**
     * 未发货订单总数
     * @return int
     */
    public function unfilledCount(){
        return OrdersARModel::find()->where([
            'buyer_id' => $this->getUserID(),
            'order_state' => 20,
            'delete_state' => 0,
            'refund_state' => 0
        ])->count();
    }
}