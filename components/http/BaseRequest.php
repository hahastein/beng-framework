<?php
/**
 * 52Beng Framework Admin
 *
 * @link http://www.52beng.com
 * @copyright Copyright © 2019 52Beng Framework. All rights reserved.
 * @author hahastein <146119@qq.com>
 * @license http://www.52beng.com/license
 * @date 2019/8/5 0:15
 */

namespace bengbeng\framework\components\http;


class BaseRequest
{
    protected $error;
    protected $postMode;
    protected $urlInfo;

    const REQUEST_MODE_POST = 'POST';
    const REQUEST_MODE_GET = 'GET';

    public function __construct($postMode = HttpUtil::POST_MODE_DEFAULT)
    {
        $this->postMode = $postMode;
    }


    protected function parsePost($data){
        if($this->postMode == HttpUtil::POST_MODE_JSON){
            return json_encode($data);
        }else{
            $postArray = array();
            foreach ($data as $key=>$value){
                array_push($postArray, $key.'='.urlencode($value));
            }
            return join('&', $postArray);
        }
    }

    protected function parseUrl($url){
        $this->urlInfo = parse_url($url);
        if(!isset($this->urlInfo["port"])){
            $this->urlInfo["port"] = 80;
        }
    }


    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}