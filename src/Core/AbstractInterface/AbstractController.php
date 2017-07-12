<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:42
 */

namespace Core\AbstractInterface;


use Core\Http\Request;
use Core\Http\Response;

abstract class AbstractController
{
    protected $actionName = null;
    protected $callArgs = null;
    function actionName($actionName = null){
        if($actionName === null){
            return $this->$actionName;
        }else{
            $this->$actionName = $actionName;
        }
    }
    abstract function index();
    abstract function onRequest($actionName);
    abstract function actionNotFound($actionName = null, $arguments = null);
    abstract function afterAction();
    function request(){
        return Request::getInstance();
    }
    function response(){
        return Response::getInstance();
    }
    function __call($actionName, $arguments)
    {
        // TODO: Implement __call() method.
        $this->actionNotFound($actionName, $arguments);
    }
}