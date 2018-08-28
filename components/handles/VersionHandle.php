<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-03
 * Time: 15:05
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\base\Enum;
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
    private $industryResource;

    private $tagLastUpdateTime;
    private $cityLastUpdateTime;
    private $industryLastUpdateTime;

    public function __construct()
    {
        $this->model = new VersionARModel();
        $this->cache = Yii::$app->cache;

        $this->tagLastUpdateTime = -1;
        $this->cityLastUpdateTime = -1;
        $this->industryResource = -1;

        $version = $this->cache->get('bengVersion');

        if ($version === false){
            $version = $this->model->findByAll();
            $this->cache->set('bengVersion', $version);
        }

        $this->version = $version;
    }

    public function getResource(){
        foreach ($this->version as $item) {
            switch ($item->version_type) {
                case self::VERSION_TYPE_API_TAG:
                    $this->tagResource = ResourceHandle::findTagAll();
                    $this->tagLastUpdateTime = $item->version_update_time;
                    break;
                case self::VERSION_TYPE_API_AREA:
                    $this->cityResource = ResourceHandle::findAreaAll(Enum::STRUCTURE_AREA_RECURSION);
                    $this->cityLastUpdateTime = $item->version_update_time;
                    break;
                case self::VERSION_TYPE_API_INDUSTRY:
                    $this->industryResource = ResourceHandle::findIndustryAll(Enum::STRUCTURE_AREA_RECURSION);
                    $this->industryLastUpdateTime = $item->version_update_time;
                    break;
            }
        }
    }

    public function getTagData(){
        return $this->tagResource;
    }

    public function getAreaData()
    {
        return $this->cityResource;
    }

    public function getIndustryData()
    {
        return $this->industryResource;
    }

    /**
     * @return mixed
     */
    public function getTagLastUpdateTime()
    {
        return $this->tagLastUpdateTime;
    }

    /**
     * @return mixed
     */
    public function getCityLastUpdateTime()
    {
        return $this->cityLastUpdateTime;
    }

    /**
     * @return mixed
     */
    public function getIndustryLastUpdateTime()
    {
        return $this->industryLastUpdateTime;
    }

}