<?php
/**
 * Created by BengBeng Framework.
 * User: hahastein
 * Date: 2018/6/19
 * Time: 23:49
 */

namespace bengbeng\framework\components\handles;

use Upyun\Config;
use Upyun\Upyun;

/**
 * Class UploadHandle
 * @property array mimes
 * @property array exts
 * @property array subName
 * @property string rootPath
 * @property string savePath
 * @property string saveExt
 * @property bool replace
 * @property bool hash
 * @property string driver
 * @property array driverConfig
 * @package bengbeng\framework\components\handles
 */

class UploadHandle
{

    const UPLOAD_TYPE_LOCAL = 'Local';
    const UPLOAD_TYPE_UPYUN = 'Upyun';

    private $config = array(
        'mimes'         =>  [], //允许上传的文件mime类型
        'exts'          =>  ['jpg', 'png'], //允许上传的文件后缀
        'subName'       =>  ['fun' => 'date', 'param' => 'Ymd'], //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  '', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  ['uniqid', ''], //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'replace'       =>  false, //存在同名是否覆盖
        'hash'          =>  true, //是否生成hash编码
        'domain'        => '',
        'driver'        =>  self::UPLOAD_TYPE_LOCAL, // 文件上传驱动
        'driverConfig'  =>  [
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'thumbnail' => true

        ], // 上传驱动配置
    );

    private $_files;
    /**
     * @var \bengbeng\framework\components\driver\upload\UploadDriverAbstract $uploader
     */
    private $uploader;

    /**
     * 错误信息
     * @var string
     */
    private $error = ''; //上传错误信息
    private $success_upload = []; //成功上传后的信息

    public function __construct($config)
    {
        $config['domain'] = \Yii::getAlias('@resUrl');
        $this->config = array_merge($this->config, $config);
        //设置rootPath和subPath
        $this->setRootPath();
        $this->setSubPath();
        //加载所有上传的文件
        $this->_files = self::loadFiles();
        //设置上传驱动模式
        $this->setDriver();
    }

    public function save($validate = true){

        if(!isset($this->_files) || count($this->_files)==0) {
            if($validate) {
                $this->error = '请选择上传的文件';
                return false;
            }else{
                return [];
            }
        }

        if(!$this->uploader){
            $this->error = "不存在上传驱动：{$this->driver}";
            return false;
        }

        if($this->subName && !function_exists($this->subName['fun'])){
            $this->error = "不存在文件生成规则：{$this->subName['fun']}";
            return false;
        }

        if($this->driver == self::UPLOAD_TYPE_LOCAL){
            if(!$this->uploader->checkRootPath($this->getRootPath())){
                $this->error = $this->uploader->getError();
                return false;
            }
        }


        /* 检查上传目录 */
        if(!$this->uploader->checkSavePath($this->savePath)){
            $this->error = $this->uploader->getError();
            return false;
        }

        $info = [];

        foreach ($this->_files as $key => $file) {
            $file['name'] = strip_tags($file['name']);
            /* 获取上传文件后缀，允许上传无后缀文件 */
            $file['ext'] = pathinfo($file['name'], PATHINFO_EXTENSION);

            $file['savename'] = $this->getName($file);
            $file['savepath'] = $this->savePath;

            if ($this->uploader->save($file, false)) {
                if(isset(\Yii::$app->params['thumbnail'])) {
                    if ($this->uploader->thumbnail($file)) {
                        unset($file['error'], $file['tmp_name']);

                    }
                    $this->error = $this->uploader->getError();
                    return false;
                }
                if(empty($this->config['domain'])){
                    $info[]['path'] = '/'.$file['savepath'].'/'.$file['savename'];
                }else{
                    $info[]['path'] = $this->config['domain'].'/'.$file['savepath'].'/'.$file['savename'];
                }
            } else {
                $this->error = $this->uploader->getError();
                return false;
            }
        }

        return $info;
    }

    public function getRootPath(){
        return isset($this->rootPath)?$this->rootPath:\Yii::getAlias('@res');
    }

    public function setSubPath(){
        if($this->savePath){
            $savePath = $this->savePath;
        }else{
            $savePath = '';
        }

        if($this->subName){
            $subPath = $this->subName;
            $subPath = call_user_func_array($subPath['fun'], (array)$subPath['param']);
            if(empty($savePath)){
                $savePath = $subPath;
            }else{
                $savePath = $savePath .'/'. $subPath;
            }
        }

        $this->config['savePath'] = $savePath;
    }

    public function getError(){
        return $this->error;
    }

    public function getSuccess(){
        return $this->success_upload;
    }

    private function getName($file){
        $newName = md5(uniqid(rand()));
        /* 文件保存后缀，支持强制更改文件后缀 */
        $ext = empty($this->config['saveExt']) ? $file['ext'] : $this->saveExt;

        return $newName .'.'. $ext;
    }

    private function setRootPath(){
        if(!isset($this->rootPath) || empty($this->rootPath)){
            $this->config['rootPath'] = \Yii::getAlias('@res');
        }
    }

    public function getUploader(){
        return $this->uploader;
    }

    /**
     * 设置上传驱动
     */
    private function setDriver(){
        $driver = $this->driver;
        $config = $this->driverConfig;
        $class = strpos($driver,'\\')? $driver : '\\bengbeng\\framework\\components\\driver\\upload\\'.ucfirst(strtolower($driver)).'Driver';
        if(class_exists($class)){
            $this->uploader = new $class($config);
        }else{
            $this->uploader = false;
        }
    }

    /**
     *
     */
    private function upyun(){

        try {


            $config = new Config(SERVICE, USER_NAME, PWD);
            $client = new Upyun($config);

            $params = [
                'notify-url' => NOTIFY_URL,
                'apps' => [
                    'name' => 'thumb',
                    'x-gmkerl-thumb' => '/format/png',
                    'save_as' => IMAGE_SAVE_AS,
                ]
            ];

            return $client->write($key, $fd, $this->config['driverConfig'], true);

        }catch (\Exception $ex){
            return false;
        }

    }

    private function check($file){
        //检查文件类型
        if(!$this->checkMime($file['type'])){
            $this->error = '类型不匹配';
            return false;
        }

        return true;
    }

    private function checkMime($mime) {
        return empty($this->mimes) ? true : in_array(strtolower($mime), $this->mimes);
    }

    private static function loadFiles(){

        $files = $_FILES;
        $fileArray  = array();
        $n          = 0;
        foreach ($files as $key=>$file){
            if(is_array($file['name'])) {
                $keys       =   array_keys($file);
                $count      =   count($file['name']);
                for ($i=0; $i<$count; $i++) {
                    if(!empty($file['name'][$i])) {
                        $fileArray[$n]['key'] = $key;
                        foreach ($keys as $_key) {
                            $fileArray[$n][$_key] = $file[$_key][$i];
                        }
                        $n++;
                    }
                }
            }else{
                if(!empty($file['name'])) {
                    $fileArray[$key] = $file;
                }
            }
        }
        return $fileArray;
    }

    public function __get($name) {
        return $this->config[$name];
    }

}