<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-02
 * Time: 17:36
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

class OrderARModel extends ActiveRecord
{
    public static function tableName(){
        return '{{%order}}';
    }

    public function rules()
    {
        return [

        ];
    }
}