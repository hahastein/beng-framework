<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 16:27
 */

namespace bengbeng\framework\models;


use yii\db\ActiveRecord;

class TagARModel extends ActiveRecord
{
    public static function tableName(){
        return '{{%tag}}';
    }

    public function data($where = false){
        $query = $this->find();
        if($where){
            $query->where($where);
        }
        return $query->all();
    }
}