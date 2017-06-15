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
	}