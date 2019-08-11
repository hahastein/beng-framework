<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\models\UserARModel;
use bengbeng\framework\models\WalletRecordARModel;
use yii\db\Exception;

class WalletLogic extends UserBase
{

    private $recordModel;

    const WALLET_MODE_BALANCE = 10;
    const WALLET_MODE_VIRTUAL_COIN = 20;
    const WALLET_MODE_POINTS = 30;

    public function __construct()
    {
        parent::__construct();
        $this->recordModel = new WalletRecordARModel();
    }


    public function signGetPoints(){
        if(!$this->recordModel->isExistTodayInfo()){

            return $this->income(20, self::WALLET_MODE_POINTS, 'sign', '签到增加');


        }else{
            $this->error = '今日已签到，不能重复签到';
            return false;
        }
    }

    public function income($currency, $mode, $tools, $tools_desc = ''){

        $transaction = \Yii::$app->db->beginTransaction();
        try{

            //获取当前积分
            $wallet = $this->getWallet();
            if($mode == self::WALLET_MODE_BALANCE){
                $orgCurrency = $wallet->balance;
            }else if($mode == self::WALLET_MODE_VIRTUAL_COIN){
                $orgCurrency = $wallet->virtualCoin;
            }else {
                $orgCurrency = $wallet->points;
            }

            //写入日志
            $this->recordModel->user_id = $this->getUserID();
            $this->recordModel->username = $this->user->nickname;
            $this->recordModel->coin = $currency;
            $this->recordModel->org_coin = $orgCurrency;
            $this->recordModel->mode = $mode;
            $this->recordModel->tools = $tools;
            $this->recordModel->tools_desc = $tools_desc;
            $this->recordModel->createtime = time();

            if($this->recordModel->save()){

                $currency = $currency + $orgCurrency;
                if($this->userModel->dataUpdate(function (ActiveOperate $operate) use ($currency){
                    $operate->where(['user_id' => $this->getUserID()]);
                    $operate->params(['points' => $currency]);
                })){
                    $transaction->commit();
                    return true;
                }else{
                    throw new Exception('入账失败');
                }

            }else{
                throw new Exception('入账记录写入失败');
            }


        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }

    }

    public function expense(){

    }

    /**
     * @return WalletProperty
     */
    public function getWallet(){
        return new WalletProperty($this->userModel->findWalletByUserID($this->getUserID()));
    }

    /**
     * 获取余额记录
     * @return array
     */
    public function balanceRecord(){
        return self::record(self::WALLET_MODE_BALANCE);
    }

    /**
     * 获取虚拟币记录
     * @return array
     */
    public function coinRecord(){
        return self::record(self::WALLET_MODE_VIRTUAL_COIN);
    }

    /**
     * 获取积分记录
     * @return array
     */
    public function pointsRecord(){
        return self::record(self::WALLET_MODE_POINTS);
    }

    /**
     * 按类型获取记录
     * @return array
     */
    public function record($mode){
        $record = $this->recordModel->findByUserIDAndMode($this->getUserID(), $mode);

        foreach ($record as $key => $item){

            $record[$key]['createtime'] = date('Y-m-d H:i', $item['createtime']);
        }
    }
}