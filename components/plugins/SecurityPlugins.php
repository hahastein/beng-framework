<?php
/**
 * Created by BengBengFramework.
 * User: hahastein
 * Date: 2018-07-10
 * Time: 18:40
 */

namespace bengbeng\framework\components\plugins;


use yii\base\Component;

class SecurityPlugins extends Component
{
    /**
     * AES解密
     * @param $orgData
     * @param $key
     * @param $iv
     * @return string
     */
    public function decrypt($orgData, $key, $iv){
        return openssl_decrypt(base64_decode($orgData), 'aes-128-cbc', $key, true, $iv);
    }
}