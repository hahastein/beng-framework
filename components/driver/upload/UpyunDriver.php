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

class UpyunDriver extends BaseUploadDriver implements UploadDriverInterface {

    private $selector;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $upyunConfig = new Config($this->sdkConfig['service'], $this->sdkConfig['user'], $this->sdkConfig['pwd']);
        $this->selector = new Upyun($upyunConfig);
    }

    public function checkRootPath($rootPath = '')
    {
        return true;
    }

    public function checkSavePath()
    {
        try{
            return $this->selector->info($this->savePath);
        }catch (\Exception $ex){
            if($this->mkdir($this->savePath)){
                $this->error = '文件夹不存在，已创建成功';
                return true;
            }else{
                $this->error = $ex->getMessage();
                return false;
            }
        }
    }

    public function upload($file, $replace = true)
    {
        return false;
    }

    public function mkdir($savePath)
    {
        return $this->selector->createDir($savePath);
    }
}