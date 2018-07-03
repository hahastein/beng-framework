<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 14:54
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * 版本模型
 * Class VersionARModel
 * @property int $version_id
 * @property int $version_type
 * @property int $version_update_time
 * @property string $current_version
 * @property bool $is_increment
 * @package bengbeng\framework\models
 */
class VersionARModel extends ActiveRecord
{
    public static function tableName(){
        return '{{%version_control}}';
    }

    public function findByAll(){
        return self::find()->all();
    }
}