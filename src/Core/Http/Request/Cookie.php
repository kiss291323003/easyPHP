<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:23
 */

namespace Core\Http\Request;


class Cookie
{
    protected static $instance;
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Cookie();
        }
        return self::$instance;
    }
    function getCookie($name = null){
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }else{
            if($name == null){
                return $_COOKIE;
            }else{
                return null;
            }
        }
    }
}