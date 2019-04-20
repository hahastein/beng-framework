<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/4/19 0:21
 */

namespace bengbeng\framework\components\driver\upload;


abstract class UploadDriverAbstract
{

    /**
     * 错误信息s
     * @var string
     */
    protected $error;
    /**
     * 上传文件根目录
     * @var string
     */
    protected $rootPath;
    /**
     * 上传文件子目录
     * @var string
     */
    protected $subPath;

    abstract function __construct($config);

    /**
     * 检查根目录是否存在
     * @param $rootPath
     * @return boolean
     */
    abstract function checkRootPath($rootPath = '');

    /**
     * 检查创建路径
     * @param $savePath
     * @return mixed
     */
    abstract function checkSavePath($savePath);
    /**
     * 创建目录
     * @param  string $savePath 要创建的路径
     * @return boolean 是否创建成功
     */
    abstract protected function mkdir($savePath);

    /**
     * 获取最后一次上传错误信息
     * @return string 错误信息
     */
    public function getError(){
        return $this->error;
    }
}