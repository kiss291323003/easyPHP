<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:48
 */

namespace Conf;


class Config
{
    protected static $instance;
    private $conf;

    function __construct()
    {
        $this->init();
    }
    /*
     * Core Instance is a singleTon in a request lifecycle
     * @return Config instance
     */
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    function getConf($key){
        if(isset($this->conf[$key])){
            return $this->conf[$key];
        }else{
            return null;
        }
    }
    function setConf($key,$val){
        $this->conf[$key] = $val;
    }

    private function init(){
        $sysConf = array(
            "DEBUG"=>array(
                "LOG"=>1,
                "DISPLAY_ERROR"=>1,
                "ENABLE"=>false,
            ),
        );
        $userConf = array(

        );
        $this->conf = $sysConf + $userConf;
    }
}