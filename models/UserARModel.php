<?php
namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * User Model
 *
 * @property integer $user_id
 * @property integer $phone_num
 * @property integer $login_type
 * @property string $username
 * @property string $userpass
 * @property string $nickname
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
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['account'] = ['username', 'userpass'];
        $scenarios['pass'] = ['phone_num','userpass'];
        $scenarios['sms'] = ['phone_num'];
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
}