<?php
/**
 * Created by PhpStorm.
 * User: hahastein
 * Date: 2018/9/15
 * Time: 23:47
 */

namespace bengbeng\framework\widgets\editor\summernote;

use yii\web\AssetBundle;

class CodemirrorAsset extends AssetBundle
{
    public $sourcePath = '@bower/codemirror';

    public $css = [
        'lib/codemirror.css',
        'theme/monokai.css'
    ];

    public $js = [
        'lib/codemirror.js',
        'mode/xml/xml.js'
    ];
}