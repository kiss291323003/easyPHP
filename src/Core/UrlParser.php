<?php
	/**
	 * Created by PhpStorm.
	 * User: 一丰
	 * Date: 2016/5/9
	 * Time: 16:21
	 */

	namespace Core;



    use Core\Http\Request\Request;

	class UrlParser
	{
        static public function pathInfo(){
            $httpRequest = Request::getInstance();
            //优先检测pathinfo模式  否则用uri路径覆盖
            $pathInfo = $httpRequest->getServer("REDIRECT_URL") ? $httpRequest->getServer("REDIRECT_URL") : '/';
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

	}