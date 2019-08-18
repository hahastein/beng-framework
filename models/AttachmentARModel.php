<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 21:01
 */

namespace bengbeng\framework\models;

use yii\db\ActiveRecord;

/**
 * Class AttachmentARModel
 * @property integer $att_id
 * @property integer $att_type
 * @property string $obj_url
 * @property integer $object_id
 * @property integer $order
 * @property integer $relation
 * @property integer $state
 * @property bool $is_default
 * @property integer $addtime
 * @package bengbeng\framework\models
 */
class AttachmentARModel extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields['obj_url'] = function () {
            if(empty($this->obj_url)){
                return \Yii::getAlias('@resUrl').'/default.png';
            }else{
                if(strpos($this->obj_url, '://')){
                    return $this->obj_url;
                }else{
                    return \Yii::getAlias('@resUrl').$this->obj_url;
                }
            }
        };
        return $fields;
    }

}