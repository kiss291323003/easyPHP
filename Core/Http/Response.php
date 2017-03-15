<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:43
 */

namespace Core\Http;


use Conf\Event;
use Core\Dispatcher;

class Response
{
    private $isEndResponse = 0;
    private $writeBuff;
    protected static $instance;
    /*
     * Core Instance is a singleTon in a request lifecycle
     * @return Response instance
     */
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Response();
        }
        return self::$instance;
    }

    function end(){
        if(!$this->isEndResponse){
            echo $this->writeBuff;
        }
        $this->isEndResponse = 1;
    }
    function isEndResponse(){
        return $this->isEndResponse;
    }
    function sendHttpStatus($code) {
         $status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if(isset($status[$code])) {
            header('HTTP/1.1 '.$code.' '.$status[$code]);
            // 确保FastCGI模式下正常
            header('Status:'.$code.' '.$status[$code]);
        }
        return $this;
    }

    function write($str){
        if(!$this->isEndResponse){
            $this->writeBuff .= $str;
        }
        return $this;
    }

    function writeJson($httpStatus,$result = null,$msg = null,$autoJsonHeader = 1){
        if($autoJsonHeader){
            $this->sendHttpStatus($httpStatus);
            //header("Content-type:application/json;charset=utf-8");
            $this->sendHeader("Content-type","application/json;charset=utf-8");
        }
        $data = Array(
            "code"=>$httpStatus,
            "result"=>$result,
            "msg"=>$msg
        );
        $this->write(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }
    function redirect($url){
        //仅支持header重定向  不做meta定向
        if (!headers_sent()) {
            // 发送302状态
            $this->sendHttpStatus(Status::CODE_MOVED_TEMPORARILY);
            $this->sendHeader("Location",$url);
            //header('Location: ' . $url);
        }
    }
    function forward($pathTo,array $get = array(),array $post = array(),array $cookies = array()){
        //change $_SERVER['REDIRECT_URL'] and add params
        $_SERVER['REDIRECT_URL'] = $pathTo;
        $_COOKIE = $cookies + $_COOKIE;
        $_GET = $get+$_GET;
        $_POST = $post+$_POST;
        $request = Request::getInstance();
        $response = Response::getInstance();
        //执行OnRequest事件
        Event::getInstance()->onRequest($request,$response);
        Dispatcher::getInstance()->dispatch($request,$response);
    }
    function setCookie($name,$value,$expire,$path = '/',$domain = null,$secure = 0){
        Cookie::setCookie($name,$value,$expire,$path,$domain,$secure);
    }
    function unsetCookie($name){
        Cookie::unsetCookie($name);
    }
    function sendHeader($key,$val){
        header($key .':'.$val);
    }
    function session(){
        return Session::getInstance();
    }

    function getResponseContent(){
        return $this->writeBuff;
    }
    function clearBuff($output = 1){
        if($output == 1){
            echo $this->writeBuff;
        }
        $this->writeBuff = null;
        return $this;
    }
}