<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:43
 */

namespace Core\Http;


use Core\Utility\Validate\Rules;
use Core\Utility\Validate\Verify;

class Request
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
    function getGet($key = null){
        if($key === null){
            return $_GET;
        }
        if(isset($_GET[$key])){
            return $_GET[$key];
        }else{
            return null;
        }
    }
    function getPost($key = null){
        if($key === null){
            return $_POST;
        }
        if(isset($_POST[$key])){
            return $_POST[$key];
        }else{
            return null;
        }
    }
    function getRequestParam($key = null){
        if($key !== null){
            $ret = self::getPost($key);
            if(empty($ret)){
                $ret = self::getGet($key);
            }
            return $ret;
        }else{
            return array_merge($this->getGet(),$this->getPost());
        }
    }
    function file(){
        return new File();
    }
    function getCookie($name = null){
        return Cookie::getCookie($name);
    }
    function session(){
        return Session::getInstance();
    }
    function getServer($key = null){
        if($key !== null){
            if(isset($_SERVER[$key])){
                return $_SERVER[$key];
            }else{
                return null;
            }
        }else{
            return $_SERVER;
        }
    }
    /*
	 * @return Validate
	*/
    function getRequestParamWithVerify(Rules $rules){
        $data = $this->getRequestParam();
        return new Verify($data,$rules);
    }
}