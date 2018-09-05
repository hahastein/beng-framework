<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/6
 * Time: 1:55
 */

namespace bengbeng\framework\widgets\dropzone;

use yii\web\AssetBundle;

class DropZoneAsset extends AssetBundle
{
    public $sourcePath = '@bower/dropzone/dist';

    public $css = [
        'min/dropzone.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];
    public $js = [
        'min/dropzone.min.js',
    ];
}