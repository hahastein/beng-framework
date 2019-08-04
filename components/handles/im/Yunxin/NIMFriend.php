<?php


namespace bengbeng\framework\components\handles\im\Yunxin;


class NIMFriend extends NIMBase
{
    /**
     * 好友关系-加好友
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $faccid        [云信ID昵称，最大长度64字节，用来PUSH推送时显示的昵称]
     * @param  $type        [用户type，最大长度256字节]
     * @param  $msg        [用户签名，最大长度256字节]
     * @return array $result      [返回array数组对象]
     */
    public function addFriend($accid,$faccid,$type='1',$msg=''){
        $url = self::NIM_API . '/friend/add.action';
        $data= array(
            'accid' => $accid,
            'faccid' => $faccid,
            'type' => $type,
            'msg' => $msg
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 好友关系-更新好友信息
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $faccid        [要修改朋友的accid]
     * @param  $alias        [给好友增加备注名]
     * @return array $result      [返回array数组对象]
     */
    public function updateFriend($accid,$faccid,$alias){
        $url = self::NIM_API . '/friend/update.action';

        $data= array(
            'accid' => $accid,
            'faccid' => $faccid,
            'alias' => $alias
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 好友关系-获取好友关系
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return array $result      [返回array数组对象]
     */
    public function getFriend($accid){
        $url = 'https://api.netease.im/nimserver/friend/get.action';
        $data= array(
            'accid' => $accid,
            'createtime' => (string)(time()*1000)
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 好友关系-删除好友信息
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $faccid        [要修改朋友的accid]
     * @return array $result      [返回array数组对象]
     */
    public function deleteFriend($accid,$faccid){
        $url = 'https://api.netease.im/nimserver/friend/delete.action';
        $data= array(
            'accid' => $accid,
            'faccid' => $faccid
        );
        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 好友关系-设置黑名单
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @param  $targetAcc        [被加黑或加静音的帐号]
     * @param  $relationType        [本次操作的关系类型,1:黑名单操作，2:静音列表操作]
     * @param  $value        [操作值，0:取消黑名单或静音；1:加入黑名单或静音]
     * @return array $result      [返回array数组对象]
     */
    public function specializeFriend($accid,$targetAcc,$relationType='1',$value='1'){
        $url = 'https://api.netease.im/nimserver/user/setSpecialRelation.action';
        $data= array(
            'accid' => $accid,
            'targetAcc' => $targetAcc,
            'relationType' => $relationType,
            'value' => $value
        );
        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }

    /**
     * 好友关系-查看黑名单列表
     * @param  $accid       [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return array $result      [返回array数组对象]
     */
    public function listBlackFriend($accid){
        $url = 'https://api.netease.im/nimserver/user/listBlackAndMuteList.action';
        $data= array(
            'accid' => $accid
        );
        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->parseReturn($result);
    }
}