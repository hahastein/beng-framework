<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-12
 * Time: 22:16
 */

namespace bengbeng\framework\models;

use bengbeng\framework\base\BaseActiveRecord;
use bengbeng\framework\base\Enum;

/**
 * Class EvaluateARModel
 * @property integer $evaluate_id
 * @property string $evaluate_content
 * @property integer $user_id
 * @property integer $star
 * @property integer $obj_id
 * @property integer $property_id
 * @property integer $status
 * @property integer $evaluate_type
 * @property integer $lookcount
 * @property integer $addtime
 * @package bengbeng\framework\models
 */

class EvaluateARModel extends BaseActiveRecord
{
    public static function tableName()
    {
        return '{{%evaluate}}';
    }

    public function getAttachment(){
        return $this->hasMany(AttachmentARModel::className(),[
            'obj_id'=>'evaluate_id'
        ])->where([
            'att_type'=>Enum::ATTACHMENT_TYPE_EVALUATE
        ]);
    }

    public function findById($user_id, $evaluate_id, $type){
        return self::find()
            ->where([
                'user_id' => $user_id,
                'evaluate_id' => $evaluate_id,
                'evaluate_type' => $type,
                'status' => Enum::EVALUATE_STATUS_SHOW
            ])
            ->with('attachment')
            ->asArray()
            ->one();
    }

    public function findAllByUserId($user_id, $type){
        return self::find()
            ->where([
                'user_id' => $user_id,
                'evaluate_type' => $type,
                'status' => Enum::EVALUATE_STATUS_SHOW
            ])
            ->with('attachment')
            ->asArray()
            ->all();
    }

    public function findAllByObjectId($object_id, $type){
        return self::find()
            ->where([
                'object_id' => $object_id,
                'evaluate_type' => $type,
                'status' => Enum::EVALUATE_STATUS_SHOW
            ])
            ->with('attachment')
            ->asArray()
            ->all();
    }
}