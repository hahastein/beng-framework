<?php
namespace bengbeng\framework\models\admin;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * 地域数据类
 * 创建者:hahastein
 * 创建时间:2018/1/16 22:50
 * @package common\bengbeng\base\model
 */
class AdminARModel extends ActiveRecord{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $pageSize = 0;
    /**
     * 关联数据库表名
     * @return string 返回数据库名称
     */
    public static function tableName()
    {
        return '{{%admin_manage}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function manageList($where=false){
        $query = self::find()->where($where);

        $query->orderBy(['admin_level'=>SORT_ASC]);

        $provider['query'] = $query;
        if($this->pageSize>0) {
            $provider['pagination'] = [
                'pageSize' => $this->pageSize
            ];
        }

        $dataProvider = new ActiveDataProvider($provider);
        return $dataProvider;
    }

    public function Info($where){
        return self::find()->where($where)->one();
    }


    public function stop($data){
        $manage = $this->info(['admin_id' => $data['id']]);
        if($manage->status == 10){
            $status = 0;
        }else{
            $status = 10;
        }
        $res = self::updateAll(['status'=>$status], ['admin_id' => $data['id']]);
        return $res;
    }

    public function loginUpdate(){
        $where = ['admin_id' => Yii::$app->user->identity->id];
        $arr = [
            'admin_login_time' => time(),
            'admin_login_num' => Yii::$app->user->identity->admin_login_num + 1
        ];
        $res = self::updateAll($arr, $where);
        return $res;
    }
}

