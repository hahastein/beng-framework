<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-02
 * Time: 17:36
 */

namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;

class OrderARModel extends BaseActiveRecord
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