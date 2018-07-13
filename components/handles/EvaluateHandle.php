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

    /**
     * @param bool $post
     * @return bool
     */
    public function setPostParams($post = false){
        if(!$post) $post = \Yii::$app->request->post();
        if(empty($post['evaluate_content'])){
            $this->error =  '评价内容不能为空';
            return false;
        }
        if(!is_numeric($post['star']) || $post['star'] > 3 || $post['star'] < 1){
            $this->error =  '评星数据错误';
            return false;
        }
        if(!is_numeric($post['obj_id']) || $post['obj_id'] < 1){
            $this->error =  '资源参数出现错误';
            return false;
        }
        $this->evaluate_content = $post['evaluate_content'];
        $this->star = $post['star'];
        $this->obj_id = $post['obj_id'];
        return true;
    }

    /**
     * @return bool|int
     * @throws Exception
     */
    public function save(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $upload = new UploadHandle([
                'savePath' => 'upload/evaluate'
            ]);
            $images = $upload->save(false);
            if(is_bool($images) && !$images) {
                throw new Exception($upload->getError());
            }

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
            $transaction->commit();
            return $this->model->evaluate_id;
        }catch (Exception $ex){
            $transaction->rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 配合第一步的Save使用，如果前端只支持单图上传时，额外的图片使用此方法传递
     * @return bool
     * @throws Exception
     */
    public function saveImgById(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $upload = new UploadHandle([
                'savePath' => 'upload/evaluate'
            ]);
            if ($images = $upload->save()) {
                foreach ($images as $image) {
                    $attModel = new AttachmentARModel();
                    $attModel->att_type = Enum::ATTACHMENT_TYPE_EVALUATE;
                    $attModel->obj_url = $image['path'];
                    $attModel->obj_id = $this->evaluate_id;
                    $attModel->addtime = time();
                    if (!$attModel->save()) {
                        throw new Exception('图片保存失败');
                    }
                }
                $transaction->commit();
                return true;
            } else {
                throw new Exception($upload->getError());
            }
        }catch (Exception $ex){
            $transaction->rollBack();
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
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    /**
     * @param int $evaluate_id
     */
    public function setEvaluateId($evaluate_id = 0)
    {
        if($evaluate_id <= 0){
            $this->evaluate_id = \Yii::$app->request->post('evaluate_id',0);
        }else {
            $this->evaluate_id = $evaluate_id;
        }
    }

    /**
     * @return int
     */
    public function getEvaluateId()
    {
        return $this->evaluate_id;
    }
}