<?php
	/**
	 * Created by PhpStorm.
	 * User: 一丰
	 * Date: 2016/10/15
	 * Time: 11:47
	 */

	namespace Core\Http;


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
		function set($key,$val){
			$_SESSION[$key] = $val;
		}
		function get($key){
			if(isset($_SESSION[$key])){
				return $_SESSION[$key];
			}else{
				return null;
			}
		}
		function delete($key){
			unset($_SESSION[$key]);
		}
		function  sessionDestroy(){
			session_destroy();
		}
		function sessionID(){
			return session_id();
		}
	}