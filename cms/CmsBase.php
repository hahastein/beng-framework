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
}