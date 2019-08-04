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


class FSockRequest extends BaseRequest implements RequestInterface
{

    private $fsock;

    public function http($url, $data, $header)
    {
        try {
            $this->parseUrl($url);

            $postData = $this->parsePost($data);

            $request = $this->requestUrl();
            $request .= $this->requestMode(self::REQUEST_MODE_POST);
            $request .= $this->requestContent(strlen($postData));
            $request .= $this->requestHeader($header);
            $request .= "\r\n";
            $request .= $postData."\r\n";


            $this->fsock = fsockopen($this->urlInfo["host"], $this->urlInfo["port"], $error_code,$error_msg);
            if(!$this->fsock) {
                throw new \Exception($error_msg);
            }
            fputs($this->fsock, $request);
            $result = '';
            while (!feof($this->fsock)) {
                $result .= fgets($this->fsock, 128);
            }
            fclose($this->fsock);
            return $result;
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }
    }

    private function requestUrl(){
        $request = "Host:".$this->urlInfo["host"]."\r\n";
        $request .= "Connection: close\r\n";
        return $request;
    }

    private function requestContent($size = 0, $charset = 'utf-8'){
        $request = "Content-type: application/x-www-form-urlencoded;charset=" . $charset . "\r\n";
        if($size > 0) {
            $request .= "Content-length: " . $size . "\r\n";
        }
        return $request;
    }

    private function requestMode($mode){
        return $mode.' '.$this->urlInfo["path"]." HTTP/1.1\r\n";
    }

    private function requestHeader($header){
        $request = '';
        foreach ($header as $key => $item){
            $request .= $key . ': ' . $item . '\r\n';
        }
        return $request;
    }
}