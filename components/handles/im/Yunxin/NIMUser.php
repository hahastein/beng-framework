<?php


namespace bengbeng\framework\components\handles\im\Yunxin;


class NIMUser extends NIMBase
{

    /**
     * 创建云信ID
     * 1.第三方帐号导入到云信平台；
     * 2.注意accid，name长度以及考虑管理秘钥token
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $name      [云信ID昵称，最大长度64字节，用来PUSH推送时显示的昵称]
     * @param  $props     [json属性，第三方可选填，最大长度1024字节]
     * @param  $icon      [云信ID头像URL，第三方可选填，最大长度1024]
     * @param  $token     [云信ID可以指定登录token值，最大长度128字节，并更新，如果未指定，会自动生成token，并在创建成功后返回]
     * @return array $result    [返回array数组对象]
     */
    public function createUserIds($accid, $name='', $props='{}', $icon='', $token=''){
        $url = $this->imUrl('create', self::URL_MODE_USER);
        $data = [
            'accid' => $accid,
            'name'  => $name,
            'props' => $props,
            'icon'  => $icon,
            'token' => $token
        ];

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 更新云信ID
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $name      [云信ID昵称，最大长度64字节，用来PUSH推送时显示的昵称]
     * @param  $props     [json属性，第三方可选填，最大长度1024字节]
     * @param  $token     [云信ID可以指定登录token值，最大长度128字节，并更新，如果未指定，会自动生成token，并在创建成功后返回]
     * @param  $icon      [云信ID头像URL，第三方可选填，最大长度1024]
     * @return array $result    [返回array数组对象]
     */
    public function updateUserId($accid,$name='',$props='{}',$token='', $icon =''){
        $url = self::NIM_API.'/user/update.action';
        $data= array(
            'accid' => $accid,
            'name'  => $name,
            'props' => $props,
            'token' => $token
        );

        if(!empty($icon)){
            $data['icon'] = $icon;
        }

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 更新并获取新token
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return array $result    [返回array数组对象]
     */
    public function updateUserToken($accid){
        $url = self::NIM_API . '/user/refreshToken.action';

        $data= array(
            'accid' => $accid
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 封禁云信ID
     * 第三方禁用某个云信ID的IM功能,封禁云信ID后，此ID将不能登陆云信imserver
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return array $result    [返回array数组对象]
     */
    public function blockUserId($accid){
        $url = self::NIM_API . '/user/block.action';
        $data= array(
            'accid' => $accid
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 解禁云信ID
     * 第三方禁用某个云信ID的IM功能,封禁云信ID后，此ID将不能登陆云信imserver
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return array $result    [返回array数组对象]
     */
    public function unblockUserId($accid){
        $url = self::NIM_API . '/user/unblock.action';
        $data= array(
            'accid' => $accid
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }


    /**
     * 更新用户名片
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $name        [云信ID昵称，最大长度64字节，用来PUSH推送时显示的昵称]
     * @param  $icon        [用户icon，最大长度256字节]
     * @param  $sign        [用户签名，最大长度256字节]
     * @param  $email       [用户email，最大长度64字节]
     * @param  $birth       [用户生日，最大长度16字节]
     * @param  $mobile      [用户mobile，最大长度32字节]
     * @param  $ex          [用户名片扩展字段，最大长度1024字节，用户可自行扩展，建议封装成JSON字符串]
     * @param  $gender      [用户性别，0表示未知，1表示男，2女表示女，其它会报参数错误]
     * @return array $result      [返回array数组对象]
     */
    public function updateUinfo($accid,$name='',$icon='',$sign='',$email='',$birth='',$mobile='',$gender='0',$ex=''){
        $url = self::NIM_API . '/user/updateUinfo.action';
        $data= array(
            'accid' => $accid,
            'name' => $name,
            'icon' => $icon,
            'sign' => $sign,
            'email' => $email,
            'birth' => $birth,
            'mobile' => $mobile,
            'gender' => $gender,
            'ex' => $ex
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 获取用户名片，可批量
     * @param array $accids    [用户帐号（例如：JSONArray对应的accid串，如："zhangsan"，如果解析出错，会报414）（一次查询最多为200）]
     * @return array $result    [返回array数组对象]
     */
    public function getUinfoss($accids){
        $url = self::NIM_API . '/user/getUinfos.action';

        $data= array(
            'accids' => json_encode($accids)
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }
}