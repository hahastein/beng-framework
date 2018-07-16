<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-16
 * Time: 12:06
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
use bengbeng\framework\models\FollowARModel;

class FollowHandle
{
    private $model;
    public $Type;
    public $user_id;
    public $obj_id;
    public function __construct()
    {
        $this->model = new FollowARModel();
        $this->Type = Enum::FOLLOW_TYPE_USER;
        $this->user_id = 0;
        $this->obj_id = 0;
    }

    /**
     * 关注操作
     */
    public function save(){
//        if($this->model = self::one()){
//
//        }else{
//            $this->model->user_id = $this->user_id;
//            $this->model->obj_id = $this->obj_id;
//            $this->model->addtime = time();
//            $this->model->status = 1;
//            if($this->model->save()){
//
//            }
//        }


    }

    /**
     * 获取关注我的列表
     */
    public function my(){

    }

    /**
     * 获取某一条关注信息
     */
    public function one(){
        return $this->model->findByUserRelation($this->user_id, $this->obj_id);
    }

    /**
     * 是否已经存在关注
     */
    public function is(){

    }

    /**
     * @param mixed $obj_id
     */
    public function setObjId($obj_id)
    {
        $this->obj_id = $obj_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @param int $Type
     */
    public function setType($Type)
    {
        $this->Type = $Type;
    }
}