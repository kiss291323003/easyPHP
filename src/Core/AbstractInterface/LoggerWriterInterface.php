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
    /*
     * @param $msg
     * @param $timeStamp
     * @return boolean isSuccess
     */
    static function writeLog($msg,$timeStamp);
}