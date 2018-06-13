<?php
namespace bengbeng\framework\components\plugins;

use yii\base\Component;

class OutputPlugins extends Component{

    public function outHtml($arr = []){
        echo '<meta charset="UTF-8"><pre style="display: block;padding: 9.5px;margin: 40px 0px 10px 0px;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">'.print_r($arr,true).'</pre>';
    }
}