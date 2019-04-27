<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 18:40
 */

namespace bengbeng\framework\components\plugins;


use yii\base\Component;

class BengPlugins extends Component
{
    const DEFAULT_OUTPUT_CONTENT = "请输出要显示的内容";

    /**
     * 输出测试数据到页面
     */
    public function outHtml(){

        $template = '<pre style="display: block;padding: 9.5px;margin: 40px 0px 10px 0px;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">{content}</pre>';
        $content = '';

        foreach (func_get_args() as $param){
            $content .= str_replace('{content}', print_r($param,true), $template);
        }

        if(empty($content)){
            $content = str_replace('{content}', print_r(self::DEFAULT_OUTPUT_CONTENT,true), $template);
        }

        \Yii::$app->response->headers->add("Content-type","text/html;charset=utf-8");
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        \Yii::$app->response->content = $content;
        \Yii::$app->state = 0;
        \Yii::$app->getResponse()->send();
        exit(0);
    }

    /**
     * AES解密
     * @param $orgData
     * @param $key
     * @param $iv
     * @return string
     */
    public function AesDecrypt($orgData, $key, $iv){
        return openssl_decrypt(base64_decode($orgData), 'aes-128-cbc', $key, true, $iv);
    }

    /*
     * 批量获取post数据
     */
    public function PostData($column = []){
        $postData = \Yii::$app->request->post();

        if(empty($postData)){
            return false;
        }

        if(empty($column)){
           return $postData;
        }

        //重置列
        foreach ($column as $key => $item){

            if (is_string($key) && array_key_exists($item, $postData)) {
                $postData[$key] = $postData[$item];
                $column[] = $key;
                unset($column[$key]);
                unset($postData[$item]);
            }

        }

        foreach ($postData as $key => $value){
            if(empty($value)){
                unset($postData[$key]);
            }else{
                if(!in_array($key, $column)){
                    unset($postData[$key]);
                }
            }
        }

        return $postData;
    }
}