<?php


namespace bengbeng\framework\cms;


class CmsBase
{
    public $cateID;
    protected $moduleModel;

    public function __construct()
    {
        $this->cateID = 0;
    }

    /**
     * @param int $cateID
     */
    public function setCateID($cateID)
    {
        $this->cateID = $cateID;
    }

    protected function parseDataAll($data){
        foreach ($data as $key => $item){
            $data[$key] = $this->parseDataOne($item);
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
        return $item;
    }
}