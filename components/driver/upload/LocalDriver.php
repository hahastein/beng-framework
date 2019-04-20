<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-06-22
 * Time: 12:16
 */

namespace bengbeng\framework\components\driver\upload;

use yii\imagine\Image;

class LocalDriver extends UploadDriverAbstract {

    /**
     * 构造函数，用于设置上传根路径
     * @param $config
     */
    public function __construct($config){

    }

    public function checkRootPath($rootPath = ''){
        if(empty($rootPath)){
            $this->error = '根目录不能为空，请创建根目录';
            return false;
        }

        if(!(is_dir($rootPath) && is_writable($rootPath))){
            $this->error = '上传根目录不存在！请尝试手动创建:'.$rootPath;
            return false;
        }
        $this->rootPath = $rootPath;
        return true;
    }

    public function checkSavePath($savePath){
        /* 检测并创建目录 */
        if (!$this->mkdir($savePath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->rootPath .'/'. $savePath)) {
                $this->error = '上传目录 ' . $savePath . ' 不可写！';
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * 保存指定文件
     * @param  array   $file    保存的文件信息
     * @param  boolean $replace 同名文件是否覆盖
     * @return boolean          保存状态，true-成功，false-失败
     */
    public function save($file, $replace=true) {
        $filename = $this->rootPath .'/'. $file['savepath'] .'/'. $file['savename'];

        /* 不覆盖同名文件 */
        if (!$replace && is_file($filename)) {
            $this->error = '存在同名文件' . $file['savename'];
            return false;
        }
        /* 移动文件 */
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            $this->error = '文件上传保存错误！';
            return false;
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
        //计算自动大小
        if($auto){
            self::autoSize($file, $zoom, $width, $height);
        }
        try {
            $path = \Yii::getAlias('@res/') . $file['savepath'] . '/thumbnail-' . $file['savename'];
            if (Image::thumbnail('@res/' . $file['savepath'] .'/'. $file['savename'], $width, $height)->save($path)) {
                return true;
            } else {
                throw new \Exception('生成缩率图失败');
            }
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    private function autoSize($fileInfo, $zoom, &$width, &$height){
        if($zoom == 0){
            if(isset(\Yii::$app->params['uploadConfig']['thumbnail']['zoom'])){
                $zoom = \Yii::$app->params['uploadConfig']['thumbnail']['zoom'];
            }
        }

        $width = $width>0?$width:\Yii::$app->params['uploadConfig']['thumbnail']['width'];
        $height = $height>0?$height:\Yii::$app->params['uploadConfig']['thumbnail']['height'];

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

    protected function mkdir($savePath){
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

}
