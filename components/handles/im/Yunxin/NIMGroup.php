<?php


namespace bengbeng\framework\components\handles\im\Yunxin;


class NIMGroup extends NIMBase
{
    /**
     * 群组功能（高级群）-创建群
     * @param  $tname       [群名称，最大长度64字节]
     * @param  $owner       [群主用户帐号，最大长度32字节]
     * @param  $members     [["aaa","bbb"](JsonArray对应的accid，如果解析出错会报414)，长度最大1024字节]
     * @param  $announcement [群公告，最大长度1024字节]
     * @param  $intro       [群描述，最大长度512字节]
     * @param  $icon        [群头像，最大长度1024字符]
     * @param  $msg       [邀请发送的文字，最大长度150字节]
     * @param  $magree      [管理后台建群时，0不需要被邀请人同意加入群，1需要被邀请人同意才可以加入群。其它会返回414。]
     * @param  $joinmode    [群建好后，sdk操作时，0不用验证，1需要验证,2不允许任何人加入。其它返回414]
     * @param  $custom      [自定义高级群扩展属性，第三方可以跟据此属性自定义扩展自己的群属性。（建议为json）,最大长度1024字节.]
     * @return bool $result
     */
    public function createGroup($tname,$owner,$members,$announcement='',$intro='',$icon='',$msg='',$magree='0',$joinmode='0',$custom='0'){
//        $url = 'https://api.netease.im/nimserver/team/create.action';
        $url = $this->imUrl('create', self::URL_MODE_TEAM);

        $data= array(
            'tname' => $tname,
            'owner' => $owner,
            'members' => $members?json_encode($members):'',
            'announcement' => $announcement,
            'intro' => $intro,
            'icon' => $icon,
            'msg' => $msg,
            'magree' => $magree,
            'joinmode' => $joinmode,
            'custom' => $custom
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->checkReturn($result);
    }

    /**
     * 群组功能（高级群）-更新群资料
     * @param  $tid       [云信服务器产生，群唯一标识，创建群时会返回，最大长度128字节]
     * @param  $owner       [群主用户帐号，最大长度32字节]
     * @param  $tname     [群名称，最大长度64字符]
     * @param  $announcement [群公告，最大长度1024字节]
     * @param  $intro       [群描述，最大长度512字节]
     * @param  $icon        [群头像，最大长度1024字符]
     * @param  $joinmode    [群建好后，sdk操作时，0不用验证，1需要验证,2不允许任何人加入。其它返回414]
     * @param  $custom      [自定义高级群扩展属性，第三方可以跟据此属性自定义扩展自己的群属性。（建议为json）,最大长度1024字节.]
     * @return bool $result
     */
    public function updateGroup($tid,$owner,$tname,$announcement='',$intro='',$icon='',$joinmode='0', $custom=''){
        $url = $this->imUrl('update', self::URL_MODE_TEAM);
        $data= array(
            'tid' => $tid,
            'owner' => $owner,
            'tname' => $tname,
            'announcement' => $announcement,
            'intro' => $intro,
            'icon' => $icon,
            'joinmode' => $joinmode,
            'custom' => $custom
        );
        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->checkReturn($result);
    }


    /**
     * 群组功能（高级群）-解散群
     * @param  $tid       [云信服务器产生，群唯一标识，创建群时会返回，最大长度128字节]
     * @param  $owner       [群主用户帐号，最大长度32字节]
     * @return bool $result
     */
    public function removeGroup($tid,$owner){
        $url = $this->imUrl('remove', self::URL_MODE_TEAM);
        $data= array(
            'tid' => $tid,
            'owner' => $owner
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->checkReturn($result);
    }

    /**
     * 群组功能（高级群）- 主动退群
     * @param  $tid       [云信服务器产生，群唯一标识，创建群时会返回，最大长度128字节]
     * @param  $accid       [退群的accid]
     * @return bool $result
     */
    public function quitGroup($tid,$accid){
        $url = $this->imUrl('leave', self::URL_MODE_TEAM);
        $data= array(
            'tid' => $tid,
            'accid' => $accid
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $this->checkReturn($result);
    }


    /**
     * 群组功能（高级群）-群信息与成员列表查询
     * @param  array $tids [群tid列表，如[\"3083\",\"3084"]]
     * @param  bool $ope   [true表示带上群成员列表，false表示不带群成员列表，只返回群信息]
     * @return bool $result
     */
    public function queryGroup($tids, $ope=true){
        $url = $this->imUrl('query', self::URL_MODE_TEAM);
        $data= array(
            'tids' => json_encode($tids),
            'ope' => $ope?'1':'0'
        );

        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $result;
    }


    /**
     * 群组功能（高级群）-拉人入群
     * @param  $tid       [云信服务器产生，群唯一标识，创建群时会返回，最大长度128字节]
     * @param  $owner       [群主用户帐号，最大长度32字节]
     * @param  $members     [["aaa","bbb"](JsonArray对应的accid，如果解析出错会报414)，长度最大1024字节]
     * @param  $magree      [管理后台建群时，0不需要被邀请人同意加入群，1需要被邀请人同意才可以加入群。其它会返回414。]
     * @param  $msg      [邀请内容]
     * @return $result      [返回array数组对象]
     */
    public function addIntoGroup($tid,$owner,$members,$magree='0',$msg='请您入伙'){
        $url = $this->imUrl('add', self::URL_MODE_TEAM);
        $data= array(
            'tid' => $tid,
            'owner' => $owner,
            'members' => json_encode($members),
            'magree' => $magree,
            'msg' => $msg
        );
        $this->checkSumHeader();
        $result = $this->httpUtil->request($url, $data, $this->httpHeader);
        return $result;
    }
}