<?php
namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * User Model
 *
 * @property integer $user_id 用户ID
 * @property integer $phone_num 用户手机号
 * @property integer $login_type 用户登录类型
 * @property string $username 用户名
 * @property string $userpass 用户密码
 * @property string $nickname 用户昵称
 * @property bool $phone_bind 是否绑定手机
 * @property string $wx_unioncode 微信第三方openid
 * @property bool $wx_bind 是否绑定微信
 * @property integer $industry_id 行业ID
 * @property integer $jobs_id 职位ID
 * @property integer $user_sex 用户性别
 * @property string $avatar_head 用户头像
 * @property integer $store_id 商户ID
 * @property integer $level 用户级别
 * @property float $balance 余额
 * @property integer $driver_type
 * @property string $driver_uuid
 * @property integer $addtime
 * @property integer $lasttime
 * @package bengbeng\framework\models
 */

class UserARModel extends ActiveRecord{

    public static function tableName(){
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            ['phone_num', 'filter', 'filter'=> 'trim', 'on'=> ['pass', 'sms']],
            ['phone_num', 'required', 'on'=> ['pass', 'sms'], 'message' => '填写手机号'],
            [['phone_num'],'match','pattern'=>'/^[1][356789][0-9]{9}$/','on'=> ['pass', 'sms'], 'message' => '手机号格式错误'],
            ['userpass', 'filter', 'filter'=> 'trim', 'on'=> ['account', 'pass']],
            ['userpass', 'required', 'on'=> ['account', 'pass'], 'message' => '请您填写密码'],
            ['userpass', 'string', 'min' => 6, 'max' => 64, 'on'=> ['account', 'pass'], 'message' => '密码位数不足'],
            ['wx_unioncode', 'required', 'on' => ['wx'], 'message' => '微信openid不能为空']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['account'] = ['username', 'userpass'];
        $scenarios['pass'] = ['phone_num','userpass'];
        $scenarios['sms'] = ['phone_num'];
        $scenarios['wx'] = ['wx_unioncode'];
        return $scenarios;
    }

    public function isByWxunion($code){
        return self::find()->where([
            'wx_unioncode' => $code
        ])->exists();
    }

    /**
     * 按微信openid获取用户信息
     * @param $code
     * @return array|UserARModel|null|ActiveRecord
     */
    public function findByWxunion($code){
        return self::info([
            'wx_unioncode' => $code
        ]);
    }

    /**
     * 按用户名获取用户信息
     * @param $username
     * @return array|UserARModel|null|ActiveRecord
     */
    public function findByUsername($username){
        return self::info([
            'username' => $username
        ]);
    }

    /**
     * 按手机号获取用户信息
     * @param $phone_num
     * @return array|UserARModel|null|ActiveRecord
     */
    public function findByMobilenumber($phone_num){
        return self::info([
            'phone_num' => $phone_num
        ]);
    }

    public function info($where = []){
        return self::find()->where($where)->one();
    }

    /**
     * 创建用户
     * @param $param
     * @return bool
     */
    public function create($param){
        $this->setScenario('wx');
        $this->setAttributes($param);
        if($this->validate()){
            $this->wx_bind = 1;
            $this->username = isset($param['username'])?$param['username']:"新用户";
            $this->nickname = $param['nickname'];
            $this->user_sex = isset($param['sex']) && is_numeric($param['sex'])?$param['sex']:1;
            $this->driver_uuid = $param['driver_uuid'];
            $this->addtime = time();
            return self::save();
        }else{
            return false;
        }
    }
}