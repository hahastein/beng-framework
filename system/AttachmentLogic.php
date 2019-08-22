<?php


namespace bengbeng\framework\system;

use bengbeng\framework\base\Modules;
use bengbeng\framework\models\AttachmentARModel;
use yii\db\Exception;

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
        var_dump($_files);die;

        try{
            $insertValue = [];
            foreach ($_files as $key => $pic){
                $item = [
                    $type,
                    $_files['originPath'],
                    $object_id,
                    time()
                ];
                if($key == 0){
                    $item[] = 1;
                }else{
                    $item[] = 0;
                }

                $insertValue[] = $item;
            }


            if( \Yii::$app->db->createCommand()->batchInsert(AttachmentARModel::tableName(), [
                'att_type',
                'obj_url',
                'object_id',
                'addtime',
                'is_default'
            ], $insertValue)->execute() ){
                return true;
            }else{
                return false;
            }
        }catch (Exception $ex){
            throw new Exception($ex->getMessage());
        }

    }
}