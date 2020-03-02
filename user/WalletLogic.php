<?php


namespace bengbeng\framework\user;


use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\models\WalletRecordARModel;
use yii\db\Exception;

/**
 * 钱包系统
 * Class WalletLogic
 * @package bengbeng\framework\user
 */
class WalletLogic extends UserBase
{

    const WALLET_MODE_BALANCE = 10;
    const WALLET_MODE_VIRTUAL_COIN = 20;
    const WALLET_MODE_POINTS = 30;

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new WalletRecordARModel();
    }

    /**
     * 检查当天是否已经签到
     * @return int|string
     */
    public function isCheckin(){
        return $this->moduleModel->isExistTodayInfo($this->getUserID(), 'intUserSign');
    }

    public function isMaxSetting($maxPoints, $tools){
        $curDayTotal = $this->moduleModel->findByTodayTotal($this->getUserID(), $tools, self::WALLET_MODE_POINTS);

//        var_dump($curDayTotal);die;
        if($curDayTotal >= $maxPoints && $maxPoints > 0){
            return false;
        }else{
            return true;
        }

    }

    public function signGetPoints($points = 20){
        if(!$this->isCheckin()){

            return $this->income($points, self::WALLET_MODE_POINTS, 'intUserSign', '签到成功');
        }else{
            $this->error = '今日已签到，不能重复签到';
            return false;
        }
    }

    public function income($currency, $mode, $tools, $tools_desc = '', $user_id=0, $nickname = ''){

        $transaction = \Yii::$app->db->beginTransaction();
        try{

            $user_id = $user_id>0?$user_id:$this->getUserID();
            //获取当前积分
            $wallet = $this->getWallet($user_id);
            if($mode == self::WALLET_MODE_BALANCE){
                $orgCurrency = $wallet->balance;
            }else if($mode == self::WALLET_MODE_VIRTUAL_COIN){
                $orgCurrency = $wallet->virtualCoin;
            }else {
                $orgCurrency = $wallet->points;
            }

            //写入日志
            $this->moduleModel->user_id = $user_id;
            $this->moduleModel->username = empty($nickname)?$this->user->nickname:$nickname;
            $this->moduleModel->coin = $currency;
            $this->moduleModel->org_coin = $orgCurrency;
            $this->moduleModel->mode = $mode;
            $this->moduleModel->tools = $tools;
            $this->moduleModel->tools_desc = $tools_desc;
            $this->moduleModel->createtime = time();

            if($this->moduleModel->save()){

                $currency = $currency + $orgCurrency;
                if($this->userModel->dataUpdate(function (ActiveOperate $operate) use ($currency, $user_id, $mode){
                    $operate->where(['user_id' => $user_id]);

                    if($mode == self::WALLET_MODE_BALANCE){
                        $operate->params(['balance' => $currency]);
                    }else if($mode == self::WALLET_MODE_VIRTUAL_COIN){
                        $operate->params(['virtualCoin' => $currency]);
                    }else {
                        $operate->params(['points' => $currency]);
                    }
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
     * 获取钱包数据
     * @param int $user_id
     * @return WalletProperty
     */
    public function getWallet($user_id = 0){
        $user_id = $user_id == 0?$this->getUserID():$user_id;
        return new WalletProperty($this->userModel->findWalletByUserID($user_id));
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
     * @param int $mode
     * @return array
     */
    public function record($mode){
        $record = $this->moduleModel->findByUserIDAndMode($this->getUserID(), $mode);
        foreach ($record as $key => $item){
            $record[$key]['createtime'] = date('Y-m-d H:i', $item['createtime']);
        }
        return $record;
    }
}