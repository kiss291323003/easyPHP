<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/3/18
 * Time: 下午8:14
 */

namespace Core\AbstractInterface;



use Core\Component\Spl\Error;

interface ErrorHandlerInterface
{
    function handler(Error $error);
    function display(Error $error);
    function log(Error $error);
}