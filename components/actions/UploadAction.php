<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/5
 * Time: 13:46
 */

namespace bengbeng\framework\components\actions;

use bengbeng\framework\base\Enum;
use bengbeng\framework\components\handles\UploadHandle;
use bengbeng\framework\models\AttachmentARModel;
use Yii;
use yii\base\Action;
use yii\db\Exception;
use yii\helpers\Json;
use yii\web\Response;

class UploadAction extends Action
{
    public $uploadModel = '';
    public $uploadDir = 'upload/';
    public $afterUploadHandler = null;
    public $beforeUploadHandler = null;
    public $isAttachment = false;
    public $outputType = Enum::OUTPUT_JSON;

    private $error = '';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->uploadDir = 'upload/';
    }

    public function run(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if(empty($this->uploadModel)){
                throw new Exception('没有设定要上传的目录');
            }
            $obj_id = 0;
            if(isset($this->beforeUploadHandler)){
                $obj_id = call_user_func($this->afterUploadHandler);
                $obj_id = !$obj_id?0:$obj_id;
            }

            $upload = new UploadHandle([
                'driverConfig' => [
                    'savePath' => $this->uploadDir . $this->uploadModel
                ]
            ]);
            $images = $upload->save();
            if($images === false && !$images) {
                throw new Exception($upload->getError());
            }

            if($this->isAttachment){
                foreach ($images as $image) {
                    $attModel = new AttachmentARModel();
                    $attModel->att_type = Enum::ATTACHMENT_TYPE_STORE;
                    $attModel->obj_url = $image['originPath'];
                    $attModel->obj_id = $obj_id;
                    $attModel->addtime = time();

                    if (!$attModel->save()) {
                        throw new Exception('图片保存失败');
                    }
                }
            }

            $transaction->commit();

            return $this->output($images);
        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return $this->output(['code' => 200, 'msg' => $this->error]);
        }
    }

    /**
     * 按类型输出内容
     * @param $data
     * @return string
     */
    private function output($data){
        switch ($this->outputType){
            case Enum::OUTPUT_JSON:
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $data;
            default:
                return $data;
        }
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}