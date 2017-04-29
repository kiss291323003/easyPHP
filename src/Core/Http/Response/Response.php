<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:22
 */

namespace Core\Http\Response;


use Conf\Event;
use Core\Dispatcher;
use Core\Http\Request\Request;
use Core\Http\Status;

class Response
{
    private $isEndResponse = 0;
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
        if($this->isEndResponse){
           return false;
        }else{
            $this->isEndResponse = 1;
            return true;
        }
    }
    function isEndResponse(){
        return $this->isEndResponse;
    }
    function sendHttpStatus($code) {
        if(!$this->isEndResponse){
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
            return true;
        }else{
            $this->isEndResponse = 1;
            return false;
        }
    }

    function write($obj){
        if(!$this->isEndResponse){
            if( is_array($obj) || is_object($obj)){
                $obj = json_encode($obj,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }
            echo $obj;
            return true;
        }else{
            return false;
        }
    }

    function writeJson($httpStatus,$result = null,$msg = null,$autoJsonHeader = 1){
        if(!$this->isEndResponse){
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
            return true;
        }else{
            return false;
        }
    }
    function redirect($url){
        if(!$this->isEndResponse){
            //仅支持header重定向  不做meta定向
            if (!headers_sent()) {
                // 发送302状态
                $this->sendHttpStatus(Status::CODE_MOVED_TEMPORARILY);
                $this->sendHeader("Location",$url);
                //header('Location: ' . $url);
            }
            return true;
        }else{
            return false;
        }
    }
    function forward($pathTo,array $get = array(),array $post = array(),array $cookies = array()){
        if(!$this->isEndResponse){
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
            return true;
        }else{
            return false;
        }
    }
    function cookie(){
        return Cookie::getInstance();
    }
    function sendHeader($key,$val){
        if(!$this->isEndResponse){
            header($key .':'.$val);
            return true;
        }else{
            return false;
        }
    }
    function session(){
        return Session::getInstance();
    }
}