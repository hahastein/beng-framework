<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 17:59
 */

namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveRecord;

/**
 * Address Model
 *
 * @property integer $user_id 用户ID
 * @property integer $address_id 地址ID
 * @property string $address
 * @property string $city
 * @property string $name
 * @property integer $phone
 * @property string $area_name
 * @property integer $addtime
 * @property  bool $is_default

 * @package bengbeng\framework\models
 */

class AddressARModel extends BaseActiveRecord
{

    public static function tableName()
    {
        return '{{%address}}';
    }

    public function rules()
    {
        return [
//            ['address_id', 'filter', 'filter'=> 'trim', 'on'=> ['modify']],
//            ['address_id', 'required', 'on'=> ['modify'], 'message' => '地址ID不正确'],
            ['user_id', 'filter', 'filter'=> 'trim', 'on'=> ['insert']],
//            ['user_id', 'required', 'on'=> ['insert', 'modify'], 'message' => '用户不正确'],
            ['address', 'required', 'on'=> ['insert', 'modify'], 'message' => '填写收获地址'],
            ['city', 'required', 'on'=> ['insert', 'modify'], 'message' => '填写收获地址所在城市'],
            ['city_name', 'required', 'on'=> ['insert', 'modify'], 'message' => '填写收获地址所在城市'],
            ['name', 'string', 'min' => 2, 'max' => 10, 'on'=> ['insert', 'modify'], 'message' => '填写收货人'],
            ['phone', 'filter', 'filter'=> 'trim', 'on'=> ['insert', 'modify']],
            ['phone', 'required', 'on'=> ['insert', 'modify'], 'message' => '填写收货人手机号'],
            [['phone'],'match','pattern'=>'/^[1][356789][0-9]{9}$/','on'=> ['insert', 'modify'], 'message' => '收货人手机号格式错误'],
            ['is_default', 'filter', 'filter'=> 'trim', 'on'=> ['insert', 'modify']]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['insert'] = ['user_id', 'address', 'city', 'name', 'city_name', 'phone', 'is_default'];
        $scenarios['modify'] = ['address', 'city', 'name', 'city_name','phone', 'is_default'];
        return $scenarios;
    }

    public function findByAddressID($address_id, $user_id = 0, $showField = array()){
        $query = self::find();
        $query->where([
            'address_id' => $address_id
        ]);
        $query->andWhere([
            'user_id' => $user_id
        ]);
        if(count($showField)>0){
            $query->select($showField);
        }
        return $query->one();
    }
}