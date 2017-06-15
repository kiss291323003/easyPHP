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
use Core\UrlParser;

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
        $uri = UrlParser::Uri();
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        foreach ($headers as $header => $val){
            $this->withAddedHeader($header,$val);
        }
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';
        $body = new Stream(fopen("php://input","r+"));
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
        parent::__construct($method, $uri, null, $body, $protocol, $_SERVER);
        $this->withCookieParams($_COOKIE)->withQueryParams($_GET)->withParsedBody($_POST)->withUploadedFiles($normalized);
    }
    function getRequestParam($key = null){
        if($key !== null){
            $this->getParsedBody($key);
            if(empty($ret)){
                $ret = $this->getQueryParam($key);
            }
            return $ret;
        }else{
            return array_merge($this->getParsedBody(),$this->getQueryParams());
        }
    }
}