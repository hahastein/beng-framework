<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/5/23 15:29
 */

namespace bengbeng\framework\models;


use bengbeng\framework\base\BaseActiveRecord;
use yii\db\ActiveQuery;

/**
 * Class ExtendARModel
 * @package bengbeng\framework\models
 * @property integer extend_id
 * @property string show_name
 * @property string extend_name
 * @property string extend_namespace
 * @property string extend_vendor_path
 * @property string extend_version
 * @property integer createtime
 * @property string extend_desc
 */
class ExtendARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%extend}}';
    }

    public function findByAll(){
        return self::dataSet(function (ActiveQuery $query){
            $query->asArray();
        });
    }

}