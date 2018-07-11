<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 17:59
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class AddressARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%address}}';
    }

    public function findByAddressID($address_id, $user_id = 0, $showField = array()){
        $query = self::find();
        $query->where([
            'address_id' => $address_id
        ]);
        if($user_id >0 ){
            $query->andWhere([
                'user_id' => $user_id
            ]);
        }
        if(count($showField)>0){
            $query->select($showField);
        }
        return $query->one();
    }
}