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

/**
 * Class HttpUtil
 * @package bengbeng\framework\components\http
 */
class HttpUtil
{

    const HTTP_REQUEST_CURL = 'CurlRequest';
    const HTTP_REQUEST_FSOCK= 'FSockRequest';

    const MODE_KEY_POST = 'post';
    const MODE_KEY_HTTP = 'http';

    const POST_MODE_DEFAULT = 0;
    const POST_MODE_JSON = 1;

    /**
     * @var RequestInterface $requestClass
     */
    private $requestClass;

    /**
     * 初始化 http util
     * HttpUtil constructor.
     * @param array $mode 初始化的参数(默认为POST_MODE_JSON和HTTP_REQUEST_CURL)
     *              说明:[
     *                    //post数据的格式 POST_MODE_DEFAULT为默认 POST_MODE_JSON为JSON格式
     *                    HttpUtil::MODE_KEY_POST => HttpUtil::POST_MODE_DEFAULT,
     *                    //http请求类型 HTTP_REQUEST_CURL为Curl方式  HTTP_REQUEST_FSOCK为 fsockopen方式
     *                    HttpUtil::MODE_KEY_HTTP => HttpUtil::HTTP_REQUEST_CURL
     *                  ]
     */
    public function __construct($mode = [])
    {

        if(!array_key_exists(self::MODE_KEY_HTTP, $mode)){
            $httpMode = self::HTTP_REQUEST_CURL;
        }else{
            $httpMode = $mode[self::MODE_KEY_HTTP];
        }

        if(!array_key_exists(self::MODE_KEY_POST, $mode)){
            $postMode = self::POST_MODE_DEFAULT;
        }else{
            $postMode = $mode[self::MODE_KEY_POST];
        }

        $className = '\\bengbeng\\framework\\components\\http\\'.$httpMode;
        $this->requestClass = new $className($postMode);
    }

    public function request($url, $data, $header){
        return $this->requestClass->http($url, $data, $header);
    }

    public function getError(){
        return $this->requestClass->getError();
    }
}