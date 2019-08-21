<?php


namespace bengbeng\framework\system;

use bengbeng\framework\base\Modules;
use bengbeng\framework\models\AttachmentARModel;

class AttachmentLogic extends Modules
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 将附件写入数据库
     * @param $_files
     * @param $object_id
     * @param $type
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save($_files, $object_id, $type){

        $insertValue = [];
        foreach ($_files as $key => $pic){
            $insertValue[] = [
                'att_type' => $type,
                'obj_url' => $_files['originPath'],
                'object_id' => $object_id,
                'addtime' => time()
            ];
            if($key == 0){
                $insertValue['is_default'] = 1;
            }
        }

        if( \Yii::$app->db->createCommand()->batchInsert(AttachmentARModel::tableName(), [
            'setting_model',
            'setting_string_value',
            'setting_int_value',
            'is_system'], $insertValue)->execute() ){
            return true;
        }else{
            return false;
        }
    }
}