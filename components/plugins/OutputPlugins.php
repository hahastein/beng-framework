<?php
namespace bengbeng\framework\components\plugins;

use yii\base\Component;

class OutputPlugins extends Component{

    const DEFAULT_OUTPUT_CONTENT = "请输出要显示的内容";

    /**
     * 输出测试数据到页面
     * @param string $output_content
     * @throws \yii\base\ExitException
     */
    public function outHtml($output_content = self::DEFAULT_OUTPUT_CONTENT){
        \Yii::$app->response->headers->add("Content-type","text/html;charset=utf-8");
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        $content = '<pre style="display: block;padding: 9.5px;margin: 40px 0px 10px 0px;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">'.print_r($output_content,true).'</pre>';
        \Yii::$app->response->content = $content;
        \Yii::$app->state = 0;
        \Yii::$app->getResponse()->send();
        exit(0);
    }
}