<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:42
 */

namespace Core\Component;


use Core\AbstractInterface\ErrorHandlerInterface;
use Core\Component\Object\Error;

class ErrorHandler implements ErrorHandlerInterface
{

    function handler(Error $error)
    {
        // TODO: Implement handler() method.
    }

    function display(Error $error)
    {
        // TODO: Implement display() method.
    }

    function log(Error $error)
    {
        // TODO: Implement log() method.
    }
}