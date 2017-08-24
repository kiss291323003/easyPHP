<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/6/15
 * Time: 下午3:55
 */

namespace Core\Http;


use Core\Http\Message\ServerRequest;
use Core\Http\Message\Stream;
use Core\Http\Message\UploadFile;
use Core\Http\Message\Uri;
use Core\UrlParser;
use Core\Utility\Validate\Validate;

class Request extends ServerRequest
{
    protected static $instance;
    /*
     * Core Instance is a singleTon in a request lifecycle
     * @return Request instance
     */
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Request();
        }
        return self::$instance;
    }

    function __construct()
    {
        $this->initHeaders();
        $uri =  $this->initUri();
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        $body = new Stream(fopen("php://input","r+"));
        $files = $this->initFiles();
        parent::__construct($method, $uri, null, $body, $protocol, $_SERVER);
        $this->withCookieParams($_COOKIE)->withQueryParams($_GET)->withParsedBody($_POST)->withUploadedFiles($files);
    }
    function getRequestParam($keyOrKeys = null, $default = null){
        if($keyOrKeys !== null){
            if(is_string($keyOrKeys)){
                $ret = $this->getParsedBody($keyOrKeys);
                if($ret === null){
                    $ret = $this->getQueryParam($keyOrKeys);
                    if ($ret === null){
                        if ($default !== null){
                            $ret = $default;
                        }
                    }
                }
                return $ret;
            }else if(is_array($keyOrKeys)){
                if (!is_array($default)){
                    $default = array();
                }
                $data = $this->getRequestParam();
                $keysNull = array_fill_keys(array_values($keyOrKeys), null);
                if($keysNull === null){
                    $keysNull = [];
                }
                $all =  array_merge($keysNull, $default, $data);
                $all = array_intersect_key($all, $keysNull);
                return $all;
            }else{
                return null;
            }
        }else{
            return array_merge($this->getParsedBody(),$this->getQueryParams());
        }
    }
    function requestParamsValidate(Validate $validate){
        return $validate->validate($this->getRequestParam());
    }
    private function initUri(){
        $url  = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '')
            . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $uri = new Uri($url);
        if(isset($_SERVER['PHP_AUTH_USER'])){
            $uri->withUserInfo($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        }
        return $uri;
    }
    private function initFiles(){
        $normalized = array();
        foreach ($_FILES as $key => $value) {
            $normalized[$key] = new UploadFile(
                $value['tmp_name'],
                (int) $value['size'],
                (int) $value['error'],
                $value['name'],
                $value['type']
            );
        }
        return $normalized;
    }
    private function initHeaders(){
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        foreach ($headers as $header => $val){
            $this->withAddedHeader($header,$val);
        }
    }
}