<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-06-22
 * Time: 12:16
 */

namespace bengbeng\framework\components\driver\upload;

use yii\imagine\Image;

class LocalDriver{

    /**
     * 上传文件根目录
     * @var string
     */
    private $rootPath;

    /**
     * 上传文件子目录
     * @var string
     */
    private $subPath;

    /**
     * 本地上传错误信息
     * @var string
     */
    private $error = ''; //上传错误信息

    /**
     * 构造函数，用于设置上传根路径
     * @param $config
     */
    public function __construct($config = null){

    }

    /**
     * 检测上传根目录
     * @param string $rootpath   根目录
     * @return boolean true-检测通过，false-检测失败
     */
    public function checkRootPath($rootpath){
        if(!(is_dir($rootpath) && is_writable($rootpath))){
            $this->error = '上传根目录不存在！请尝试手动创建:'.$rootpath;
            return false;
        }
        $this->rootPath = $rootpath;
        return true;
    }

    /**
     * 检测上传目录
     * @param  string $savepath 上传目录
     * @return boolean          检测结果，true-通过，false-失败
     */
    public function checkSavePath($savepath){
        /* 检测并创建目录 */
        if (!$this->mkdir($savepath)) {
            return false;
        } else {
            /* 检测目录是否可写 */
            if (!is_writable($this->rootPath .'/'. $savepath)) {
                $this->error = '上传目录 ' . $savepath . ' 不可写！';
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
     * @param bool $auto
     * @param int $width
     * @param int $height
     * @return bool
     */
    public function thumbnail($file, $auto = true, $width=0, $height=0){
        //计算自动大小
        if($auto){
            self::autoSize($auto, $width, $height);
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

    private function autoSize($auto, &$width, &$height){
        if($auto){
//            \Yii::$app->params['']
        }
        $width=100;
        $height=50;
    }

    /**
     * 创建目录
     * @param  string $savepath 要创建的穆里
     * @return boolean          创建状态，true-成功，false-失败
     */
    public function mkdir($savepath){
        $dir = $this->rootPath .'/'. $savepath;
        if(is_dir($dir)){
            return true;
        }

        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$savepath} 创建失败！";
            return false;
        }
    }

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }

}
