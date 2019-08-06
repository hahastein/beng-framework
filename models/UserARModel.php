<?php
namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\data\ActiveOperate;
use bengbeng\framework\enum\UserEnum;
use yii\data\Pagination;
use yii\db\ActiveQuery;
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
 * @property string $wx_unioncode 微信第三方unionid
 * @property string $wx_openid 微信openid
 * @property bool $wx_bind 是否绑定微信
 * @property integer $industry_id 行业ID
 * @property integer $jobs_id 职位ID
 * @property integer $intro 用户简介
 * @property integer $user_sex 用户性别
 * @property string $avatar_head 用户头像
 * @property integer $store_id 商户ID
 * @property integer $level 用户级别
 * @property float $balance 余额
 * @property integer $driver_type
 * @property string $driver_uuid
 * @property integer $addtime
 * @property integer $lasttime
 * @property string $auth_key
 * @package bengbeng\framework\models
 */

class UserARModel extends BaseActiveRecord {

    public static function tableName(){
        return '{{%user}}';
    }

    public function getImToken(){
        return $this->hasOne(ImTokenARModel::className(),['user_id'=>'user_id'])->select('unionid,im_token');
    }

    public function rules()
    {
        return [
            ['phone_num', 'filter', 'filter'=> 'trim', 'on'=> ['pass', 'sms', 'modifyWithPhone']],
            ['phone_num', 'required', 'on'=> ['pass', 'sms'], 'message' => '填写手机号'],
            [['phone_num'],'match','pattern'=>'/^[1][356789][0-9]{9}$/','on'=> ['pass', 'sms'], 'message' => '手机号格式错误'],
            ['userpass', 'filter', 'filter'=> 'trim', 'on'=> ['account', 'pass']],
            ['userpass', 'required', 'on'=> ['account', 'pass'], 'message' => '请您填写密码'],
            ['userpass', 'string', 'min' => 6, 'max' => 64, 'on'=> ['account', 'pass'], 'message' => '密码位数不足'],
            ['wx_unioncode', 'required', 'on' => ['wx'], 'message' => '微信openid不能为空'],
            ['nickname', 'required', 'on' => ['modify', 'modifyWithPhone'], 'message' => '昵称不能为空'],
            ['nickname', 'string', 'min' => 2, 'max' => 18, 'on' => ['modify', 'modifyWithPhone'], 'message' => '昵称不能小于2位，多以18位']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['account'] = ['username', 'userpass'];
        $scenarios['pass'] = ['phone_num','userpass'];
        $scenarios['sms'] = ['phone_num'];
        $scenarios['wx'] = ['wx_unioncode'];
        $scenarios['modifyWithPhone'] = ['phone_num'];
        $scenarios['modify'] = ['phone_num', 'nickname'];
        return $scenarios;
    }

    public function fields()
    {
        $fields = parent::fields(); // TODO: Change the autogenerated stub
        unset($fields['auth_key'], $fields['userpass'], $fields['password_reset_token']);
        return $fields;
    }

    /**
     * 微信UnionCode是否存在
     * @param bool $code        微信UnionCode
     * @param bool $needInfo    是否要返回相应数据 默认为不返回数据
     * @return bool|array
     */
    public function isByWxunion($code, $needInfo = false){
        if($needInfo){
            return self::findByWxunion($code);
        }else{
            return self::find()
                ->where([
                    'wx_unioncode' => $code
                ])
                ->exists();
        }
    }

    /**
     * 按微信UnionCode获取用户信息
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

    public function findAllByUnionId($unionid){

        return self::dataOne(function (ActiveQuery $query) use($unionid){
            $query->with(['imToken']);
            $query->where([
                'unionid' => $unionid
            ]);
        });
    }


    /**
     * 按手机号或者微信code进行查找用户信息
     * @param $phone_num
     * @param $code
     * @return array|null|ActiveRecord
     */
    public function findByMobileAndWxcode($phone_num,$code){
        return self::info([
            'or',
            ['phone_num' => $phone_num],
            ['wx_unioncode' => $code]
        ]);
    }

    public function findByAll(){
        return self::dataSet();
    }

    /**
     * 更新unionID
     * @param $userID
     * @param $unionID
     * @return bool
     */
    public function updateUnionID($userID, $unionID){
        return self::dataUpdate(function (ActiveOperate $operate) use($userID, $unionID){
            $operate->params(['unionid' => $unionID]);
            $operate->where(['user_id' => $userID]);
        });
    }

    /**
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password){
        $this->userpass = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function generateAuthKey(){
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    /**
     * 创建用户
     * @param $param
     * @return bool
     * @throws \yii\base\Exception
     */
    public function create($param){
        $this->setScenario('wx');
        $this->setAttributes($param);
        if($this->validate()){
            $this->login_type = isset($param['login_type'])?$param['login_type']:UserEnum::LOGIN_TYPE_MOBILE_SMS;
            $this->wx_bind = isset($param['wx_unioncode'])?1:0;
            $this->phone_bind = isset($param['phone_bind'])?1:0;
            if(isset($param['userpass']))$this->userpass = $param['userpass'];
            $this->username = isset($param['username'])?$param['username']:"新用户";
            isset($param['userpass'])?$this->setPassword($param['userpass']):$this->userpass = "";
            $this->generateAuthKey();
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