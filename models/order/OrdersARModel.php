<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-02
 * Time: 17:36
 */

namespace bengbeng\framework\models\order;

use bengbeng\framework\base\BaseActiveRecord;

/**
 * Class OrderARModel
 *
 * @package bengbeng\framework\models
 */
class OrdersARModel extends BaseActiveRecord
{
    public static function tableName(){
        return '{{%shop_orders}}';
    }

}