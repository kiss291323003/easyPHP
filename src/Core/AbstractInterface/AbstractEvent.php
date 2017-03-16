<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:41
 */

namespace Core\AbstractInterface;


use Core\Http\Request;
use Core\Http\Response;

abstract class AbstractEvent
{
    protected static $instance;
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    abstract function frameInitialize();
    abstract function onRequest(Request $request,Response $response);
    abstract function onDispatcher(Request $request,Response $response,$targetControllerClass,$targetAction);
    abstract function afterResponse(Request $request,Response $response);
}