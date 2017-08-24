<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午5:40
 */

namespace Core\AbstractInterface;


interface LoggerWriterInterface
{
    static function writeLog($obj,$logCategory,$timeStamp);
}