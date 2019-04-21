<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/4/21 0:33
 */

namespace bengbeng\framework\components\driver\upload;

/**
 * Class BaseUploadDriver
 * @property string $rootPath 上传的根目录(只在本地有效)
 * @property string $savePath 上传的存储路径
 * @property array $folderNameMode 文件夹的命名方式(设置后则在savePath后在创建folderNameMode的方式生成的文件夹)
 * @property array $fileNameMode 文件的命名方式
 * @property string $fileExt 文件的扩展名(默认使用原文件的后缀)
 * @property boolean $replace 是否替换存在的文件，默认不替换
 * @property boolean|array $thumbnail 是否创建缩略图，缩略图参数配置如下 zoom:比例 width:宽度 height:高度
 * @property array $sdkConfig sdk的服务配置(非本地上传使用)
 * @package bengbeng\framework\components\driver\upload
 */
class BaseUploadDriver
{
    /**
     * 错误信息
     * @var string
     */
    protected $error;

//    /**
//     * 上传文件根目录
//     * @var string
//     */
//    protected $rootPath;

//    /**
//     * 上传文件子目录
//     * @var string
//     */
//    protected $savePath='';

    /**
     * 上传成功后的文件地址
     * @var string
     */
    protected $uploadOriginPath;
    /**
     * 上传成功后的缩略图地址
     * @var string
     */
    protected $uploadThumbnailPath;

    /**
     * 配置项
     * @var array
     */
    protected $config;

    public function __construct($config){
        $this->config = $config;
        $this->setRootPath();
        $this->setSubPath();
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    protected function setRootPath(){
        if(empty($this->rootPath)){
            $this->rootPath = \Yii::getAlias('@res');
        }
    }

    protected function setSubPath(){
        var_dump($this->savePath);
        if($this->folderNameMode){
            $subPath = call_user_func_array($this->folderNameMode['fun'], (array)$this->folderNameMode['param']);
        }else{
            $subPath = date('Ym');
        }

        if(empty($this->savePath) ){
            $this->savePath = $subPath;
        }else{
            $this->savePath = $this->savePath .'/'. $subPath;
        }

        var_dump($this->savePath);
    }

    /**
     * 生成上传后存入的文件名
     * @param $file
     * @return string
     */
    protected function getName($file){
        if($this->fileNameMode){
            $newName = call_user_func_array($this->fileNameMode['fun'], (array)$this->fileNameMode['param']);
        }else{
            $newName = md5(uniqid(rand()));
        }
        /* 文件保存后缀，支持强制更改文件后缀 */
        $ext = empty($this->fileExt) ? $file['ext'] : $this->fileExt;

        return $newName .'.'. $ext;
    }

    /**
     * @return string
     */
    public function getUploadOriginPath()
    {
        return $this->uploadOriginPath;
    }

    /**
     * @return string
     */
    public function getUploadThumbnailPath()
    {
        return $this->uploadThumbnailPath;
    }

    public function __get($name)
    {
        return $this->config[$name];
    }
}