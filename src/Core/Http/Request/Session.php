<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:23
 */

namespace Core\Http\Request;


use Core\Component\Di;
use Core\Component\SysConst;

class Session
{
    private static $self_instance;
    function __construct()
    {
        $handler = Di::getInstance()->get(SysConst::DI_SESSION_HANDLER);
        if($handler instanceof \SessionHandlerInterface){
            session_set_save_handler($handler,true);
        }
        $this->startSession();
    }
    static function getInstance(){
        if(isset(self::$self_instance)){
            return 	self::$self_instance;
        }else{
            self::$self_instance = new Session();
            return 	self::$self_instance;
        }
    }
    private function startSession(){
        session_start();
    }
    function get($key){
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }else{
            return null;
        }
    }
    function sessionID(){
        return session_id();
    }
}