<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/21
 * Time: 11:27
 */

namespace Core\AbstractInterface;


abstract class AbstractShutdownHandler
{
    protected $lastError;//error_get_last();
    function __construct()
    {
        $this->lastError = error_get_last();
    }
    function getLastError(){
        return $this->lastError;
    }
    abstract function handler();
}