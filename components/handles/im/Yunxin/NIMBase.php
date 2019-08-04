<?php


namespace bengbeng\framework\components\handles\im\Yunxin;


use bengbeng\framework\base\Enum;
use bengbeng\framework\components\http\HttpUtil;

class NIMBase
{
    private $AppKey;                //开发者平台分配的AppKey
    private $AppSecret;             //开发者平台分配的AppSecret,可刷新

    const NIM_API = 'https://api.netease.im/nimserver';
    const HEX_DIGITS = "0123456789abcdef";

    const URL_MODE_USER = 'user';
    const URL_MODE_FRIEND = 'friend';
    const URL_MODE_TEAM = 'team';

    protected $postType;
    protected $httpUtil;
    protected $httpHeader;

    /**
     * 参数初始化
     * @param $postType [选择请求方式，fsockopen或curl,若为curl方式，请检查php配置是否开启]
     */
    public function __construct($postType = Enum::IM_REQUEST_POST_TYPE_CURL){
        $config = \Yii::$app->params['Yunxin'];
        $this->AppKey    = $config['key'];
        $this->AppSecret = $config['secret'];

        if($postType == Enum::IM_REQUEST_POST_TYPE_FSOCK) {
            $this->postType = HttpUtil::HTTP_REQUEST_FSOCK;
        }else{
            $this->postType = HttpUtil::HTTP_REQUEST_CURL;
        }

        $this->httpUtil = new HttpUtil([
            HttpUtil::MODE_KEY_HTTP => $this->postType
        ]);

        $this->httpHeader = [
            'AppKey' => $config['key'],
            'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8'
        ];
    }

    protected function checkSumHeader(){
        $nonce = '';
        for($i=0;$i<128;$i++){			//随机字符串最大128个字符，也可以小于该数
            $nonce.= self::HEX_DIGITS[rand(0,15)];
        }
        $this->httpHeader['Nonce'] = $nonce;
        $this->httpHeader['CurTime'] = (string)time();
        $this->httpHeader['CheckSum'] = sha1($this->AppSecret.$this->httpHeader['Nonce'].$this->httpHeader['CurTime']);
    }

    protected function imUrl($action, $mode = self::URL_MODE_USER){
        return self::NIM_API . '/' . $mode . '/' . $action . '.action';
    }

    /**
     * 解析返回数据
     * @param $result
     * @return array
     */
    protected function parseReturn($result){

        if($this->postType == HttpUtil::HTTP_REQUEST_FSOCK){
            $str_s = strpos($result,'{');
            $str_e = strrpos($result,'}');
            $result = substr($result, $str_s,$str_e-$str_s+1);
        }

        return $this->json_to_array($result);
    }


    /**
     * 将json字符串转化成php数组
     * @param  $json_str
     * @return array $json_arr
     */
    private function json_to_array($json_str){
        // version 1.6 code ...
        // if(is_null(json_decode($json_str))){
        //     $json_str = $json_str;
        // }else{
        //     $json_str = json_decode($json_str);
        // }
        // $json_arr=array();
        // //print_r($json_str);
        // foreach($json_str as $k=>$w){
        //     if(is_object($w)){
        //         $json_arr[$k]= $this->json_to_array($w); //判断类型是不是object
        //     }else if(is_array($w)){
        //         $json_arr[$k]= $this->json_to_array($w);
        //     }else{
        //         $json_arr[$k]= $w;
        //     }
        // }
        // return $json_arr;

        if(is_array($json_str) || is_object($json_str)){
//            $json_str = $json_str;
        }else if(is_null(json_decode($json_str))){
//            $json_str = $json_str;
        }else{
            $json_str =  strval($json_str);
            $json_str = json_decode($json_str,true);
        }
        $json_arr=array();
        foreach($json_str as $k=>$w){
            if(is_object($w)){
                $json_arr[$k]= $this->json_to_array($w); //判断类型是不是object
            }else if(is_array($w)){
                $json_arr[$k]= $this->json_to_array($w);
            }else{
                $json_arr[$k]= $w;
            }
        }
        return $json_arr;
    }
}