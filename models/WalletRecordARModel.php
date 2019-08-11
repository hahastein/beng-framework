<?php


namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * Class WalletRecordARModel
 * @property integer $record_id
 * @property integer $user_id           用户ID
 * @property string $username           用户名称
 * @property integer $admin_id          管理员ID-管理员发放用
 * @property float $coin                增减数-负数为扣除
 * @property float $org_coin            更改前的数据
 * @property integer $mode              模式-10余额20虚拟币30积分
 * @property integer $tools             操作功能 功能参看说明或者查看const标注
 * @property string $tools_desc         操作说明
 * @property integer $createtime        创建时间
 * @package bengbeng\framework\models
 */
class WalletRecordARModel extends BaseActiveRecord
{

    public static function tableName()
    {
        return '{{%wallet_record}}';
    }

    public function isExistTodayInfo(){
        $start = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        return self::find()->andFilterWhere(['between', 'createtime', $start, $end])->count();
    }

    public function findByUserIDAndMode($userID, $mode){
        return $this->dataSet(function (ActiveQuery $query) use($userID, $mode){
            $query->select(['username', 'coin', 'mode', 'tools', 'tools_desc']);
            $query->where([
                'user_id' => $userID,
                'mode' => $mode
            ]);
            $query->asArray();
        });
    }
}