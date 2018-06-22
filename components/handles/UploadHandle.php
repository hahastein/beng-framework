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

    public function __construct($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function save(){

        switch ($this->config['driver']){
            case self::UPLOAD_TYPE_LOCAL:

                $this->local();
                break;
            case self::UPLOAD_TYPE_UPYUN:

                $this->upyun();
                break;
            default:

                break;
        }
    }

    private function local(){

    }

    /**
     *
     */
    private function upyun(){
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

        $client->write($key, $fd, $params, true);

    }

    private static function _files(){

        $file  = $_FILES;
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