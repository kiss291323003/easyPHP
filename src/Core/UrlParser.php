<?php
	/**
	 * Created by PhpStorm.
	 * User: 一丰
	 * Date: 2016/5/9
	 * Time: 16:21
	 */

	namespace Core;


	use Core\Http\Request;

	class UrlParser
	{
        static public function parser(Request $httpRequest){
            //优先检测pathinfo模式  否则用uri路径覆盖
            $pathInfo = $httpRequest->getServer("REDIRECT_URL") ? $httpRequest->getServer("REDIRECT_URL") : '/';
            //反编码
            $pathInfo = rawurldecode($pathInfo);
            //去除尾缀   如.html .do
            $post = strripos($pathInfo,'.');
            if($post !== false){
                $pathInfo = substr($pathInfo,0,$post);
            }
            //去除右边 index
            if(substr($pathInfo,-6) == '/index'){
                $pathInfo = substr($pathInfo,0,-5);
            }
            //去除右边 /    在空路径时   请勿去除
            if(strlen($pathInfo) > 1){
                $pathInfo = rtrim($pathInfo,"/");
            }
            return $pathInfo;
        }
	}