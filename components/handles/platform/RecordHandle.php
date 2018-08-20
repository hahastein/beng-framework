<?php
/**
 * Created by PhpStorm.
 * User: bengbeng
 * Date: 2018/8/20
 * Time: 上午11:42
 */

namespace bengbeng\framework\components\handles\platform;


use bengbeng\framework\models\platform\PlatformRecordARModel;

class RecordHandle
{
    private $_model;
    private $error;
    public $mode;

    public $user_id;
    public $admin_id;
    public $update_value;
    public $before_value;
    public $des;

    const RECORD_MODE_MYSQL = 1;
    const RECORD_MODE_FILE = 2;
    const RECODE_MODE_REDIS = 3;

    public function __construct()
    {
        $this->_model = new PlatformRecordARModel();
        $this->mode = self::RECORD_MODE_MYSQL;
        $this->admin_id = 0;
        $this->des = 0;
        $this->user_id = 0;
        $this->before_value = 0;
    }

    public function write(){
        if(empty($this->update_value)){
            $this->error = '没有要写入的记录值';
            return false;
        }
        switch ($this->mode){
            case self::RECORD_MODE_MYSQL:
                $this->_model->record_time = time();
                $this->_model->before_value = $this->before_value;
                $this->_model->update_value = $this->update_value;
                $this->_model->des = $this->des;
                $this->_model->update_user_id = $this->user_id;
                $this->_model->operate_id = $this->admin_id;
                if($this->_model->save()){
                    return true;
                }else{
                    $this->mode = self::RECORD_MODE_FILE;
                    return self::write();
                }

                break;
            default:
                $this->error = '文本写入失败。';
                return false;
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}