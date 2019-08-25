<?php
namespace bengbeng\framework\models\admin;

use bengbeng\framework\base\BaseActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * 地域数据类
 * 创建者:hahastein
 * 创建时间:2018/1/16 22:50
 * @package common\bengbeng\base\model
 */
class AdminARModel extends BaseActiveRecord implements IdentityInterface{

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

    public static function findIdentity($id){
        return static::find()->where([
            'admin_id' => $id,
            'status' => self::STATUS_ACTIVE
        ])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * 按用户名查找管理员信息
     * @param $username
     * @return mixed
     */
    public static function findByUsername($username){
        return static::findOne(['admin_name' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 查找token
     * @param $token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId(){
        return $this->getPrimaryKey();
    }

    public function getAuthKey(){
        return $this->auth_key;
    }

    /**
     * 验证密钥
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 验证密码
     * @param $password
     * @param $password_hash
     * @return mixed
     */
    public function validatePassword($password, $password_hash){
        return Yii::$app->security->validatePassword($password, $password_hash);
    }

    /**
     * 设置密码
     * @param $password
     */
    public function setPassword($password){
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * 通用密钥
     */
    public function generateAuthKey(){
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(){
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(){
        $this->password_reset_token = null;
    }

    /**
     * 返回所有管理员信息
     * @return array
     */
    public function findByAll(){

        return self::dataSet(function (ActiveQuery $query){
            $query->asArray();
        });

    }

    /**
     * 停用账号
     * @param $id
     * @return bool
     */
    public function stop($id){
        $manage = self::findIdentity($id);
        if(isset($manage)){
            return self::updateAll([
                'status' => 0
            ], [
                'admin_id' => $id
            ]);
        }
        return false;
    }

    /**
     * 更新账号登录信息
     * @return mixed
     */
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

