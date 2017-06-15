<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:42
 */

namespace Core\Component;


use Core\AbstractInterface\ErrorHandlerInterface;
use Core\Component\Spl\SplError;
use Core\Http\Response;

class ErrorHandler implements ErrorHandlerInterface
{

    function handler(SplError $error)
    {
        // TODO: Implement handler() method.
    }

    function display(SplError $error)
    {
        // TODO: Implement display() method.
        Response::getInstance()->write($error->__toString());
    }

    function log(SplError $error)
    {
        // TODO: Implement log() method.
        Logger::log($error);
    }
}