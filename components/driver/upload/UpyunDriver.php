<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-06-22
 * Time: 12:16
 */

namespace bengbeng\framework\components\driver\upload;

use Upyun\Config;
use Upyun\Upyun;

class UpyunDriver extends UploadDriverAbstract {

    private $selector;

    public function __construct($config = [])
    {

        $upyunConfig = new Config($config['service'], $config['user'], $config['pwd']);
        $this->selector = new Upyun($upyunConfig);
    }

    public function checkRootPath($rootPath = '')
    {
        return true;
    }

    public function checkSavePath($savePath)
    {

        return $this->mkdir($savePath);
//        $pathInfo = false;
//        try{
//            $pathInfo = $this->selector->info($savePath);
//        }catch (\Exception $ex){
//            $this->error = $ex->getMessage();
//        }
//        return $pathInfo;
    }

    protected function mkdir($savePath)
    {
        return $this->selector->createDir($savePath);
    }
}