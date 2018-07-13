<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-13
 * Time: 11:37
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use bengbeng\framework\models\AttachmentARModel;
use bengbeng\framework\models\EvaluateARModel;
use yii\db\Exception;

class EvaluateHandle
{
    private $model;

    private $evaluate_id;
    private $user_id;

    private $evaluate_content;
    private $star;
    private $obj_id;

    private $error;

    public function __construct()
    {
        $this->model = new EvaluateARModel();
        $this->evaluate_id = 0;
        $this->user_id;
    }

    public function setPostParams($post = false){
        $post = \Yii::$app->request->post();
        $this->evaluate_content = $post['evaluate_content'];
        $this->star = $post['star'];
        $this->obj_id = $post['resource_id'];
    }

    public function save(){
        $upload = new UploadHandle([
            'savePath' => 'upload/evaluate'
        ]);

        try {
            if ($images = $upload->save()) {
                $this->model->evaluate_content = $this->evaluate_content;
                $this->model->star = $this->star;
                $this->model->user_id = $this->user_id;
                $this->model->obj_id = $this->obj_id;
                $this->model->evaluate_type = 10;
                $this->model->addtime = time();

                if (!$this->model->save()) {
                    throw new Exception('评价保存失败');
                }

                foreach ($images as $image) {

                    $attModel = new AttachmentARModel();
                    $attModel->att_type = Enum::ATTACHMENT_TYPE_EVALUATE;
                    $attModel->obj_url = $image['path'];
                    $attModel->obj_id = $this->model->evaluate_id;
                    $attModel->addtime = time();

                    if (!$attModel->save()) {
                        throw new Exception('图片保存失败');
                    }
                }

                return $this->model->evaluate_id;
            } else {
                throw new Exception($upload->getError());
            }
        }catch (Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param int $evaluate_id
     */
    public function setEvaluateId($evaluate_id)
    {
        $this->evaluate_id = $evaluate_id;
    }
}