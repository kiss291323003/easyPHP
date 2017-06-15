<?php
	/**
	 * Created by PhpStorm.
	 * User: 一丰
	 * Date: 2016/5/9
	 * Time: 16:21
	 */

	namespace Core;



    use Core\Http\Message\Uri;
    use Core\Http\Request;

	class UrlParser
	{
        static public function pathInfo(){
            $pathInfo = Request::getInstance()->getUri()->getPath();
            $basePath = dirname($pathInfo);
            $info = pathInfo($pathInfo);
            if($info['filename'] != 'index'){
                if($basePath == '/'){
                    $basePath = $basePath.$info['filename'];
                }else{
                    $basePath = $basePath.'/'.$info['filename'];
                }
            }
            return $basePath;
        }
        static public function Uri(){
            $url  = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '')
                . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
            $uri = new Uri($url);
            if(isset($_SERVER['PHP_AUTH_USER'])){
                $uri->withUserInfo($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
            }
            return $uri;
        }
	}