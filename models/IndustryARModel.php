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

    public function data($where = false)
    {
        $query = $this->find();
        if ($where) {
            $query->where($where);
        }
        return $query->all();
    }

}
