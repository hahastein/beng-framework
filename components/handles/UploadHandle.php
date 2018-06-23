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

class UploadHandle
{

    const UPLOAD_TYPE_LOCAL = 1;
    const UPLOAD_TYPE_UPYUN = 2;

    private $config = array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'autoSub'       =>  true, //自动子目录保存文件
        'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  '../Uploads/', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'replace'       =>  false, //存在同名是否覆盖
        'hash'          =>  true, //是否生成hash编码
        'driver'        =>  self::UPLOAD_TYPE_LOCAL, // 文件上传驱动
        'driverConfig'  =>  array(), // 上传驱动配置
    );

    private $_files;

    /**
     * 错误信息
     * @var string
     */
    private $error = ''; //上传错误信息

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        p($this->config);
        $this->_files = self::loadFiles();
    }

    public function save(){
        if(!isset($this->_files) || count($this->_files)==0){
            $this->error = '请选择上传的文件';
            return false;
        }

        switch ($this->config['driver']){
            case self::UPLOAD_TYPE_LOCAL:
                return $this->local();
            case self::UPLOAD_TYPE_UPYUN:
                return $this->upyun();
            default:
                $this->error = '没有此上传驱动';
                return false;
        }
    }

    public function getError(){
        return $this->error;
    }

    private function local(){
        return false;
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
        return empty($this->config['mimes']) ? true : in_array(strtolower($mime), $this->config['mimes']);
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
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key){
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            }else{
                $fileArray = $files;
                break;
            }
        }
        return $fileArray;
    }

//    public function __set($key, $value)
//    {
//        $this->$key = $value;
//    }
//
//    public function __get($key){
//        if (isset($key)){
//            return $this->$key;
//        }else {
//            return NULL;
//        }
//    }
}