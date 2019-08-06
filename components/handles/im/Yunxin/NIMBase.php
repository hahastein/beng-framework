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

    public $error;

    private $codeDesc = [
        '201' => '客户端版本不对，需升级sdk',
        '301' => '被封禁',
        '302' => '用户名或密码错误',
        '315' => 'IP限制',
        '403' => '非法操作或没有权限',
        '404' => '对象不存在',
        '405' => '参数长度过长',
        '406' => '对象只读',
        '408' => '客户端请求超时',
        '413' => '验证失败(短信服务)',
        '414' => '参数错误',
        '415' => '客户端网络问题',
        '416' => '频率控制',
        '417' => '重复操作',
        '418' => '通道不可用(短信服务)',
        '419' => '数量超过上限',
        '422' => '账号被禁用',
        '423' => '帐号被禁言',
        '431' => 'HTTP重复请求',
        '500' => '服务器内部错误',
        '503' => '服务器繁忙',
        '508' => '消息撤回时间超限',
        '509' => '无效协议',
        '514' => '服务不可用',
        '998' => '解包错误',
        '999' => '打包错误',
        '801' => '群人数达到上限',
        '802' => '没有权限',
        '803' => '群不存在',
        '804' => '用户不在群',
        '805' => '群类型不匹配',
        '806' => '创建群数量达到限制',
        '807' => '群成员状态错误',
        '808' => '申请成功',
        '809' => '已经在群内',
        '810' => '邀请成功',
        '811' => '@账号数量超过限制',
        '812' => '群禁言，普通成员不能发送消息',
        '813' => '群拉人部分成功',
        '814' => '禁止使用群组已读服务',
        '815' => '群管理员人数超过上限',
        '9102' => '通道失效',
        '9103' => '已经在他端对这个呼叫响应过了',
        '11001' => '通话不可达，对方离线状态',
        '13001' => 'IM主连接状态异常',
        '13002' => '聊天室状态异常',
        '13003' => '账号在黑名单中,不允许进入聊天室',
        '13004' => '在禁言列表中,不允许发言',
        '13005' => '用户的聊天室昵称、头像或成员扩展字段被反垃圾',
        '10431' => '输入email不是邮箱',
        '10432' => '输入mobile不是手机号码',
        '10433' => '注册输入的两次密码不相同',
        '10434' => '企业不存在',
        '10435' => '登陆密码或帐号不对',
        '10436' => 'app不存在',
        '10437' => 'email已注册',
        '10438' => '手机号已注册',
        '10441' => 'app名字已经存在'
    ];

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

    protected function checkReturn($result){

        if(!is_array($result)){
            $result = $this->parseReturn($result);
        }

        if(array_key_exists('code', $result)){
            if($result['code'] == 200){
                return true;
            }
        }

        if(array_key_exists($result['code'], $this->codeDesc)){
            $this->error = $this->codeDesc[$result['code']] . '' . (isset($result['desc'])?$result['desc']:'');
        }else{
            $this->error = '未找到错误描述';
        }

        return false;
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