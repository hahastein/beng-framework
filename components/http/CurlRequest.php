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

class CurlRequest extends BaseRequest implements RequestInterface
{

    private $curl;
    private $timeout;

    public function __construct($postMode = HttpUtil::POST_MODE_DEFAULT)
    {
        parent::__construct($postMode);

        $this->curl = curl_init();
        $this->timeout = 5000;
    }

    /**
     * @param $url
     * @param int $outputPage 是否直接输出到页面
     */
    private function requestUrl($url, $outputPage = 1){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $outputPage);
    }

    private function requestPostData($data){
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parsePost($data));
    }

    private function requestSSLVerify(){
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
    }

    /**
     * @param $header
     * @param bool $returnRequestHeader 是否返回请求头信息
     */
    private function requestHeader($header, $returnRequestHeader = false){
        curl_setopt($this->curl, CURLOPT_HEADER, $returnRequestHeader);

        $request = [];
        foreach ($header as $key => $item){
            $request[] = $key . ':' . $item;
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER,$request);
    }

    /**
     * @param $url
     * @param $data
     * @param $header
     * @return bool
     */
    public function http($url, $data, $header)
    {
        try{
            if(!$url){
                throw new \Exception('非法请求Url，请检查Url完整性');
            }

            $this->requestUrl($url);
            $this->requestHeader($header);
            $this->requestSSLVerify();
            $this->requestPostData($data);

            $result = curl_exec($this->curl);
            if (false === $result) {
                throw new \Exception($this->requestError());
            }

            $this->close();
            return $result;
        }catch (\Exception $ex){
            $this->error = $ex->getMessage();
            return false;
        }

    }

    private function close(){
        curl_close($this->curl);
    }

    private function requestError(){
        $code = curl_errno($this->curl);
        $codeContent = [
            '1'=>'CURLE_UNSUPPORTED_PROTOCOL (1) – 您传送给 libcurl 的网址使用了此 libcurl 不支持的协议。 可能是您没有使用的编译时选项造成了这种情况（可能是协议字符串拼写有误，或没有指定协议 libcurl 代码）。',
            '2'=>'CURLE_FAILED_INIT (2) – 非常早期的初始化代码失败。 可能是内部错误或问题。',
            '3'=>'CURLE_URL_MALFORMAT (3) – 网址格式不正确。',
            '5'=>'CURLE_COULDNT_RESOLVE_PROXY (5) – 无法解析代理服务器。 指定的代理服务器主机无法解析。',
            '6'=>'CURLE_COULDNT_RESOLVE_HOST (6) – 无法解析主机。 指定的远程主机无法解析。',
            '7'=>'CURLE_COULDNT_CONNECT (7) – 无法通过 connect() 连接至主机或代理服务器。',
            '8'=>'CURLE_FTP_WEIRD_SERVER_REPLY (8) – 在连接到 FTP 服务器后，libcurl 需要收到特定的回复。 此错误代码表示收到了不正常或不正确的回复。 指定的远程服务器可能不是正确的 FTP 服务器。',
            '9'=>'CURLE_REMOTE_ACCESS_DENIED (9) – 我们无法访问网址中指定的资源。 对于 FTP，如果尝试更改为远程目录，就会发生这种情况。',
            '11'=>'CURLE_FTP_WEIRD_PASS_REPLY (11) – 在将 FTP 密码发送到服务器后，libcurl 需要收到正确的回复。 此错误代码表示返回的是意外的代码。',
            '13'=>'CURLE_FTP_WEIRD_PASV_REPLY (13) – libcurl 无法从服务器端收到有用的结果，作为对 PASV 或 EPSV 命令的响应。 服务器有问题。',
            '14'=>'CURLE_FTP_WEIRD_227_FORMAT (14) – FTP 服务器返回 227 行作为对 PASV 命令的响应。如果 libcurl 无法解析此行，就会返回此代码。',
            '15'=>'CURLE_FTP_CANT_GET_HOST (15) – 在查找用于新连接的主机时出现内部错误。',
            '17'=>'CURLE_FTP_COULDNT_SET_TYPE (17) – 在尝试将传输模式设置为二进制或 ascii 时发生错误。',
            '18'=>'CURLE_PARTIAL_FILE (18) – 文件传输尺寸小于或大于预期。当服务器先报告了一个预期的传输尺寸，然后所传送的数据与先前指定尺寸不相符时，就会发生此错误。',
            '19'=>'CURLE_FTP_COULDNT_RETR_FILE (19) – ‘RETR’ 命令收到了不正常的回复，或完成的传输尺寸为零字节。',
            '21'=>'CURLE_QUOTE_ERROR (21) – 在向远程服务器发送自定义 “QUOTE” 命令时，其中一个命令返回的错误代码为 400 或更大的数字（对于 FTP），或以其他方式表明命令无法成功完成。',
            '22'=>'CURLE_HTTP_RETURNED_ERROR (22) – 如果 CURLOPT_FAILONERROR 设置为 TRUE，且 HTTP 服务器返回 >= 400 的错误代码，就会返回此代码。 （此错误代码以前又称为 CURLE_HTTP_NOT_FOUND。）',
            '23'=>'CURLE_WRITE_ERROR (23) – 在向本地文件写入所收到的数据时发生错误，或由写入回调 (write callback) 向 libcurl 返回了一个错误。',
            '25'=>'CURLE_UPLOAD_FAILED (25) – 无法开始上传。 对于 FTP，服务器通常会拒绝执行 STOR 命令。错误缓冲区通常会提供服务器对此问题的说明。 （此错误代码以前又称为 CURLE_FTP_COULDNT_STOR_FILE。）',
            '26'=>'CURLE_READ_ERROR (26) – 读取本地文件时遇到问题，或由读取回调 (read callback) 返回了一个错误。',
            '27'=>'CURLE_OUT_OF_MEMORY (27) – 内存分配请求失败。此错误比较严重，若发生此错误，则表明出现了非常严重的问题。',
            '28'=>'CURLE_OPERATION_TIMEDOUT (28) – 操作超时。 已达到根据相应情况指定的超时时间。',
            '30'=>'CURLE_FTP_PORT_FAILED (30) – FTP PORT 命令返回错误。 在没有为 libcurl 指定适当的地址使用时，最有可能发生此问题。 请参阅 CURLOPT_FTPPORT。',
            '31'=>'CURLE_FTP_COULDNT_USE_REST (31) – FTP REST 命令返回错误。如果服务器正常，则应当不会发生这种情况。',
            '33'=>'CURLE_RANGE_ERROR (33) – 服务器不支持或不接受范围请求。',
            '34'=>'CURLE_HTTP_POST_ERROR (34) – 此问题比较少见，主要由内部混乱引发。',
            '35'=>'CURLE_SSL_CONNECT_ERROR (35) – 同时使用 SSL/TLS 时可能会发生此错误。您可以访问错误缓冲区查看相应信息，其中会对此问题进行更详细的介绍。可能是证书（文件格式、路径、许可）、密码及其他因素导致了此问题。',
            '36'=>'CURLE_FTP_BAD_DOWNLOAD_RESUME (36) – 尝试恢复超过文件大小限制的 FTP 连接。',
            '37'=>'CURLE_FILE_COULDNT_READ_FILE (37) – 无法打开 FILE:// 路径下的文件。原因很可能是文件路径无法识别现有文件。 建议您检查文件的访问权限。',
            '38'=>'CURLE_LDAP_CANNOT_BIND (38) – LDAP 无法绑定。LDAP 绑定操作失败。',
            '39'=>'CURLE_LDAP_SEARCH_FAILED (39) – LDAP 搜索无法进行。',
            '41'=>'CURLE_FUNCTION_NOT_FOUND (41) – 找不到函数。 找不到必要的 zlib 函数。',
            '42'=>'CURLE_ABORTED_BY_CALLBACK (42) – 由回调中止。 回调向 libcurl 返回了 “abort”。',
            '43'=>'CURLE_BAD_FUNCTION_ARGUMENT (43) – 内部错误。 使用了不正确的参数调用函数。',
            '45'=>'CURLE_INTERFACE_FAILED (45) – 界面错误。 指定的外部界面无法使用。 请通过 CURLOPT_INTERFACE 设置要使用哪个界面来处理外部连接的来源 IP 地址。 （此错误代码以前又称为 CURLE_HTTP_PORT_FAILED。）',
            '47'=>'CURLE_TOO_MANY_REDIRECTS (47) – 重定向过多。 进行重定向时，libcurl 达到了网页点击上限。请使用 CURLOPT_MAXREDIRS 设置上限。',
            '48'=>'CURLE_UNKNOWN_TELNET_OPTION (48) – 无法识别以 CURLOPT_TELNETOPTIONS 设置的选项。 请参阅相关文档。',
            '49'=>'CURLE_TELNET_OPTION_SYNTAX (49) – telnet 选项字符串的格式不正确。',
            '51'=>'CURLE_PEER_FAILED_VERIFICATION (51) – 远程服务器的 SSL 证书或 SSH md5 指纹不正确。',
            '52'=>'CURLE_GOT_NOTHING (52) – 服务器未返回任何数据，在相应情况下，未返回任何数据就属于出现错误。',
            '53'=>'CURLE_SSL_ENGINE_NOTFOUND (53) – 找不到指定的加密引擎。',
            '54'=>'CURLE_SSL_ENGINE_SETFAILED (54) – 无法将选定的 SSL 加密引擎设为默认选项。',
            '55'=>'CURLE_SEND_ERROR (55) – 无法发送网络数据。',
            '56'=>'CURLE_RECV_ERROR (56) – 接收网络数据失败。',
            '58'=>'CURLE_SSL_CERTPROBLEM (58) – 本地客户端证书有问题',
            '59'=>'CURLE_SSL_CIPHER (59) – 无法使用指定的密钥',
            '60'=>'CURLE_SSL_CACERT (60) – 无法使用已知的 CA 证书验证对等证书',
            '61'=>'CURLE_BAD_CONTENT_ENCODING (61) – 无法识别传输编码',
            '62'=>'CURLE_LDAP_INVALID_URL (62) – LDAP 网址无效',
            '63'=>'CURLE_FILESIZE_EXCEEDED (63) – 超过了文件大小上限',
            '64'=>'CURLE_USE_SSL_FAILED (64) – 请求的 FTP SSL 级别失败',
            '65'=>'CURLE_SEND_FAIL_REWIND (65) – 进行发送操作时，curl 必须回转数据以便重新传输，但回转操作未能成功',
            '66'=>'CURLE_SSL_ENGINE_INITFAILED (66) – SSL 引擎初始化失败',
            '67'=>'CURLE_LOGIN_DENIED (67) – 远程服务器拒绝 curl 登录（7.13.1 新增功能）',
            '68'=>'CURLE_TFTP_NOTFOUND (68) – 在 TFTP 服务器上找不到文件',
            '69'=>'CURLE_TFTP_PERM (69) – 在 TFTP 服务器上遇到权限问题',
            '70'=>'CURLE_REMOTE_DISK_FULL (70) – 服务器磁盘空间不足',
            '71'=>'CURLE_TFTP_ILLEGAL (71) – TFTP 操作非法',
            '72'=>'CURLE_TFTP_UNKNOWNID (72) – TFTP 传输 ID 未知',
            '73'=>'CURLE_REMOTE_FILE_EXISTS (73) – 文件已存在，无法覆盖',
            '74'=>'CURLE_TFTP_NOSUCHUSER (74) – 运行正常的 TFTP 服务器不会返回此错误',
            '75'=>'CURLE_CONV_FAILED (75) – 字符转换失败',
            '76'=>'CURLE_CONV_REQD (76) – 调用方必须注册转换回调',
            '77'=>'CURLE_SSL_CACERT_BADFILE (77) – 读取 SSL CA 证书时遇到问题（可能是路径错误或访问权限问题）',
            '78'=>'CURLE_REMOTE_FILE_NOT_FOUND (78) – 网址中引用的资源不存在',
            '79'=>'CURLE_SSH (79) – SSH 会话中发生无法识别的错误',
            '80'=>'CURLE_SSL_SHUTDOWN_FAILED (80) – 无法终止 SSL 连接'
        ];
        return array_key_exists($code, $codeContent)?$codeContent[$code]:'未找到指定错误，错误码为：'.$code;
    }


}