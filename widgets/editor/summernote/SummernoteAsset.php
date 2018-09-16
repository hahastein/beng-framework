<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/15
 * Time: 23:36
 */

namespace bengbeng\framework\widgets\editor\summernote;

use yii\web\AssetBundle;

class SummernoteAsset extends AssetBundle
{
    public $sourcePath = '@bower/summernote/dist';

    public $css = [
        'summernote.css',
    ];

    public $depends = [];

    public $js = [
        'summernote.js',
    ];

}