<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-06-22
 * Time: 12:16
 */

namespace bengbeng\framework\components\driver\upload;

use yii\imagine\Image;

class LocalDriver extends BaseUploadDriver implements UploadDriverInterface {

    /**
     * 构造函数，用于设置上传根路径
     * @param $config
     */
    public function __construct($config){

        parent::__construct($config);


    }

    public function checkRootPath(){
        if(empty($this->rootPath)){
            $this->error = '根目录不能为空，请创建根目录';
            return false;
        }

        if(!(is_dir($this->rootPath) && is_writable($this->rootPath))){
            $this->error = '上传根目录不存在！请尝试手动创建:' . $this->rootPath;
            return false;
        }
        return true;
    }

    public function checkSavePath(){
        /* 检测并创建目录 */
        if (!$this->mkdir($this->savePath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->rootPath .'/'. $this->savePath)) {
                $this->error = '上传目录 ' . $this->savePath . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
    }

    public function mkdir($savePath){
        $dir = $this->rootPath .'/'. $savePath;
        if(is_dir($dir)){
            return true;
        }

        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savePath} 创建失败！";
            return false;
        }
    }

    public function upload($file, $replace=true)
    {
        $this->uploadOriginPath = '';
        $saveName = $this->getName($file);
        $this->uploadOriginPath = $this->rootPath . '/' . $this->savePath . '/' .$saveName;

        /* 不覆盖同名文件 */
        if (!$replace && is_file($this->uploadOriginPath)) {
            $this->error = '存在同名文件' . $saveName;
            return false;
        }
        /* 移动文件 */
        if (!move_uploaded_file($file['tmp_name'], $this->uploadOriginPath)) {
            $this->error = '文件上传保存错误！';
            return false;
        }
        $this->uploadOriginPath = '/' . $this->savePath . '/' .$saveName;

        if($this->thumbnail){
            if (!$this->thumbnail($file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 生成所缩略图
     * @param $file
     * @param int $zoom
     * @param int $width
     * @param int $height
     * @return bool
     */
    public function thumbnail($file, $zoom = 0, $width=0, $height=0){
        $this->uploadThumbnailPath = '';
        //计算自动大小
        $zoom = $this->getThumbnailZoom($zoom);
        $width = $this->getThumbnailWidth($width);
        $height = $this->getThumbnailHeight($height);

        self::autoSize($file, $zoom, $width, $height);

        try {
            $saveName = $this->getName($file);
            $originPath = $this->rootPath . '/' . $this->savePath . '/' . $saveName;
            $this->uploadThumbnailPath = $this->rootPath . '/' . $this->savePath . '/t'.$width.'_' . $saveName;
            if (Image::thumbnail($originPath, $width, $height)->save($this->uploadThumbnailPath)) {
                $this->uploadThumbnailPath = '/' . $this->savePath . '/t'.$width.'_' . $saveName;
                return true;
            } else {
                throw new \Exception('生成缩略图失败');
            }
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    /**
     * 配置缩略图比例
     * @param $zoom
     * @return float
     */
    private function getThumbnailZoom($zoom){
        if($zoom == 0){
            if(isset($this->thumbnail['zoom'])){
                $zoom = $this->thumbnail['zoom'];
            }
        }
        return $zoom;
    }

    /**
     * 配置缩略图的宽度
     * @param $width
     * @return float 返回配置的宽度
     */
    private function getThumbnailWidth($width){
        return $width>0?$width:$this->thumbnail['width'];
    }

    /**
     * 配置缩略图的高度
     * @param $height
     * @return float 返回配置的高度
     */
    private function getThumbnailHeight($height){
        return $height>0?$height:$this->thumbnail['height'];
    }

    /**
     * 自动计算缩略图的大小
     * @param array $fileInfo 图片信息
     * @param float $zoom 缩略图比列
     * @param float &$width 缩略图宽度并返回新的宽度
     * @param float &$height 缩略图高度并返回新的高度
     */
    private function autoSize($fileInfo, $zoom, &$width, &$height){

        if($zoom >0){
            $width = $fileInfo['width'] * 100 / $zoom;
            $height = $fileInfo['height'] * 100 / $zoom;
        }else if($height==0){
            //按宽度自动设置
            if($fileInfo['width']>0 && $fileInfo['height']>0) {
                if ($fileInfo['width'] >= $width) {
                    $height = ($fileInfo['height'] * $width) / $fileInfo['width'];
                }
            }
        }else if($width==0){
            //按高度自动设置
            if($fileInfo['width']>0 && $fileInfo['height']>0) {
                if ($fileInfo['height'] >= $height) {
                    $width = ($fileInfo['width'] * $height) / $fileInfo['height'];
                }
            }
        }
    }

}
