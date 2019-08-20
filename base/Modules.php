<?php


namespace bengbeng\framework\base;


use bengbeng\framework\components\helpers\UrlHelper;

class Modules
{
    /**
     * @var int $cateID 分类ID
     */
    protected $cateID;

    protected $moduleModel;

    public function __construct()
    {
        $this->cateID = UrlHelper::param('cate_id', 0);
        $this->init();
    }

    protected function init(){

    }

    /**
     * @param int $cateID
     */
    public function setCateID($cateID)
    {
        $this->cateID = $cateID;
    }

    protected function parseDataAll($data, $callbak = false){
        foreach ($data as $key => $item){
            $data[$key] = $callbak?$this->$callbak($item):$this->parseDataOne($item);
        }
        return $data;
    }

    protected function parseDataOne($item){
        if(isset($item['createtime'])) {
            $item['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
        }
        if(isset($item['updatetime'])) {
            $item['updatetime'] = date('Y-m-d H:i:s', $item['updatetime']);
        }

        if(isset($item['user_id'])){
            unset($item['user_id']);
        }

        if(isset($item['user']['user_id'])){
            unset($item['user']['user_id']);
        }
        return $item;
    }
}