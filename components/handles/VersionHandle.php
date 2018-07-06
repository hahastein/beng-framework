<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 15:05
 */

namespace bengbeng\framework\components\handles;

use Yii;
use bengbeng\framework\models\VersionARModel;

class VersionHandle
{
    const VERSION_TYPE_APP_IOS = 1;
    const VERSION_TYPE_APP_ANDROID = 2;
    const VERSION_TYPE_API_AREA = 3;
    const VERSION_TYPE_API_TAG = 4;
    const VERSION_TYPE_API_CATE = 5;
    const VERSION_TYPE_API_INDUSTRY = 6;
    const VERSION_TYPE_API_JOBS = 7;

    private $model;
    private $cache;
    private $version;

    private $tagResource;
    private $cityResource;

    public function __construct()
    {
        $this->model = new VersionARModel();
        $this->cache = Yii::$app->cache;

        $version = $this->cache->get('bengVersion');

        if ($version === false){
            $version = $this->model->findByAll();
            $this->cache->set('bengVersion', $version, 3600);
        }
    }

    public function getResource(){
//        p($this->version);die;
        foreach ($this->version as $item) {
            switch ($item->version_type) {
                case self::VERSION_TYPE_API_TAG:
                    $this->tagResource = ResourceHandle::findTagAll();
                    break;
                case self::VERSION_TYPE_API_AREA:
                    $this->cityResource = ResourceHandle::findAreaAll();
                    break;
            }
        }
    }

    public function getTagData(){
        return $this->tagResource;
    }

    public function getAreaData(){
        return $this->cityResource;
    }

}