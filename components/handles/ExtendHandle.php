<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/5/23 19:24
 */

namespace bengbeng\framework\components\handles;

use bengbeng\framework\models\ExtendARModel;
use yii\helpers\ArrayHelper;

/**
 * Class ExtendHandle
 * @package bengbeng\framework\components\handles
 */
class ExtendHandle
{

    private $model;
    private $extensions_path;
    public $extensions;


    public function __construct()
    {
        $this->model = new ExtendARModel();
        $this->extensions_path = \Yii::getAlias('@vendor/bengbengsoft/extensions.php');
        $this->extensions = is_file($this->extensions_path) ? include $this->extensions_path : $this->createFile();
    }

    /**
     * 创建扩展文件
     * @return array
     */
    public function createFile(){
        $data = $this->model->findByAll();
        $data = $this->formatForYiiExtend($data);
        $this->writeFile($data);
        return $data;
    }

    /**
     * 追加内容到文件
     * @param array $content
     * @return bool|int
     */
    public function appendFile($content){
        $content = $this->formatForYiiExtend([$content]);

        $resetExtend = $this->resetExtendPath($this->extensions);
        $this->extensions = ArrayHelper::merge($resetExtend, $content);
        return $this->writeFile($this->extensions);
    }

    /**
     * 写入文件
     * @param $content
     * @return bool|int
     */
    public function writeFile($content){
        $content = $text='<?php '.PHP_EOL.'$vendorDir = dirname(__DIR__); '.PHP_EOL.'return '.var_export($content,true).';';
        $content = str_replace('\'[vendorPath]', '$vendorDir . \'', $content);
        return file_put_contents($this->extensions_path, print_r($content, true));
    }

    private function resetExtendPath($extensions){

        foreach ($extensions as $index => $extend){
            foreach ($extend['alias'] as $key => $alias)
            $extensions[$index]['alias'][$key] = '[vendorPath]/'.$extend['name'];
        }

        return $extensions;
    }

    /**
     * 将数据格式化为Yii extend 的格式
     * @param $data
     * @return array
     */
    private function formatForYiiExtend($data){

        $yiiExtend = [];
        foreach ($data as $item){

            $extend_name = 'bengbeng-extend/'.$item['extend_name'];
            $yiiExtend[$extend_name] = [
                'name' => $extend_name,
                'version' => $item['extend_version'],
                'alias' => [
                    '@'.$item['extend_namespace'] => '[vendorPath]/' . $extend_name
                ]
            ];
        }

        return $yiiExtend;
    }


}