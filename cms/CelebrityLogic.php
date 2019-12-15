<?php


namespace bengbeng\framework\cms;

use bengbeng\framework\models\cms\CelebrityARModel;

class CelebrityLogic extends CmsBase
{

    public function __construct()
    {
        parent::__construct();
        $this->moduleModel = new CelebrityARModel();
    }

    public function user(){

        $this->all(Cms::CELEBRITY_MODE_USER);
    }


    private function all($mode){

    }
}