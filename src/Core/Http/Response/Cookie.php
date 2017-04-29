<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:23
 */

namespace Core\Http\Response;


class Cookie
{
    protected static $instance;
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new Cookie();
        }
        return self::$instance;
    }
    function setCookie($name,$value,$expire,$path = '/',$domain = null,$secure = 0){
        if(Response::getInstance()->isEndResponse()){
            return false;
        }else{
            setcookie($name,$value,$expire,$path,$domain,$secure);
            return true;
        }
    }
    function unsetCookie($name){
        if(Response::getInstance()->isEndResponse()){
            return false;
        }else{
            $this->setCookie($name,'',time()-100);
            return true;
        }
    }
}