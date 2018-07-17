<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-17
 * Time: 17:35
 */

namespace bengbeng\framework\components\handles\im\Yunxin;


class YunxinCheckSum
{
// 计算并获取CheckSum
public static getCheckSum($appSecret, $appSecret, String curTime) {
return encode("sha1", appSecret + nonce + curTime);
}
}