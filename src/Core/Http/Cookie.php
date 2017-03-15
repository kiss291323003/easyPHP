<?php
	/**
	 * Created by PhpStorm.
	 * User: 一丰
	 * Date: 2016/10/15
	 * Time: 11:46
	 */

	namespace Core\Http;


	class Cookie
	{
        static function getCookie($name = null){
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
        static function setCookie($name,$value,$expire,$path = '/',$domain = null,$secure = 0){
			setcookie($name,$value,$expire,$path,$domain,$secure);
		}
        static function unsetCookie($name){
			self::setCookie($name,'',time()-100);
		}
	}