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
        $this->extensions = is_file($this->extensions_path) ? include $this->extensions_path : $this->createCache();
    }

    public function createCache(){

        $data = $this->model->findByAll();
        $data = $this->formatForYiiExtend($data);

        $this->readOrCreateFile($data);

        return $data;

    }



    public function readOrCreateFile($content){
        $content = $text='<?php '.PHP_EOL.'$vendorDir = dirname(__DIR__); '.PHP_EOL.'return '.var_export($content,true).';';
        $content = str_replace('\'[vendorPath]', '$vendorDir . \'', $content);
        return file_put_contents($this->extensions_path, print_r($content, true));
    }

    /**
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