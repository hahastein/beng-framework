<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/9
 * Time: 20:05
 */

namespace bengbeng\framework\widgets\dropzone;

use yii\web\AssetBundle;

class DropZoneTemplateAsset extends AssetBundle
{
    public $css=[
        'dist/dropzone.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR.'/assets/';
    }
}