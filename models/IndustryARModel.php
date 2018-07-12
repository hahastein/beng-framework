<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 14:50
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class IndustryARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%industry}}';
    }

    public function getChild(){
        return $this->hasMany(self::className(),['parent_id'=>'industry_id']);
    }

    public function data($where = false, $order = false)
    {
        $query = $this->find();
        if ($where) {
            $query->where($where);
        }
        if($order){
            $query->orderBy($order);
        }
        return $query->all();
    }

}
