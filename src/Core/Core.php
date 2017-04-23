<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午5:26
 */

namespace Core;


use Conf\Config;
use Conf\Event;
use Core\AbstractInterface\ErrorHandlerInterface;
use Core\AbstractInterface\ExceptionHandlerInterface;
use Core\Component\Di;
use Core\Component\ErrorHandler;
use Core\Component\Logger;
use Core\Component\Spl\SplError;
use Core\Component\SysConst;
use Core\Http\Request\Request;
use Core\Http\Response\Response;

class Core
{
    protected static $instance;
    /*
     * Core Instance is a singleTon in a request lifecycle
     * @param callable $preHandler  callable before frameWork initialize
     * @return Core instance
     */
    static function getInstance(callable $preHandler = null){
        if(!isset(self::$instance)){
            self::$instance = new static($preHandler);
        }
        return self::$instance;
    }
    /*
     * construct func for Core Instance
     * @param callable $preHandler  callable before frameWork initialize
     */
    function __construct(callable $preHandler = null)
    {
        if(is_callable($preHandler)){
            call_user_func($preHandler);
        }
    }

    /*
     * run in web server model
     */
    function run(){
        $request = Request::getInstance();
        $response = Response::getInstance();
        Event::getInstance()->onRequest($request,$response);

        Dispatcher::getInstance()->dispatch($request,$response);
        $response->end();
        Event::getInstance()->afterResponse($request,$response);
    }

    /*
     * initialize frameWork
     */
     function frameWorkInitialize(){
        $this->defineSysConst();
        $this->registerAutoLoader();
        $this->setDefaultAppDirectory();
        Event::getInstance()->frameInitialize();
        $this->registerErrorHandler();
        $this->registerFatalErrorHandler();
        $this->registerExceptionHandler();
        return $this;
    }

    /*
     * register php auto loader
     */
    private function registerAutoLoader(){
        require_once __DIR__."/AutoLoader.php";
        $loader = AutoLoader::getInstance();
        //添加系统核心目录
        $loader->addNamespace("Core","Core");
        //添加conf目录
        $loader->addNamespace("Conf","Conf");
        //添加系统依赖组件
        $loader->addNamespace("FastRoute","Core/Vendor/FastRoute");
        $loader->addNamespace("SuperClosure","Core/Vendor/SuperClosure");
        $loader->addNamespace("PhpParser","Core/Vendor/PhpParser");
    }

    private function defineSysConst(){
        define("ROOT",realpath(__DIR__.'/../'));
    }

    private function registerErrorHandler(){
        $conf = Config::getInstance()->getConf("DEBUG");
        if($conf['ENABLE'] == true){
            set_error_handler(function($errorCode, $description, $file = null, $line = null, $context = null)use($conf){
                $error = new SplError($errorCode, $description, $file, $line, $context);
                $errorHandler = Di::getInstance()->get(SysConst::DI_ERROR_HANDLER);
                if(!is_a($errorHandler,ErrorHandlerInterface::class)){
                    $errorHandler = new ErrorHandler();
                }
                $errorHandler->handler($error);
                if($conf['DISPLAY_ERROR'] == true){
                    $errorHandler->display($error);
                }
                if($conf['LOG'] == true){
                    $errorHandler->log($error);
                }
            });
        }
    }
    private function registerFatalErrorHandler(){
        $conf = Config::getInstance()->getConf("DEBUG");
        if($conf['ENABLE']){
            register_shutdown_function(function(){
                $error = error_get_last();
                if(!empty($error)){
                    $error = new SplError(E_ERROR,$error['message'],$error['file'],$error['line']);
                    var_dump(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
                }
            });
        }
    }
    private function registerExceptionHandler(){
        $handler = Di::getInstance()->get(SysConst::DI_EXCEPTION_HANDLER);
        if($handler instanceof  ExceptionHandlerInterface){
            set_exception_handler(function(\Exception $exception)use($handler){
                $handler->handler($exception);
            });
        }
    }
    private function setDefaultAppDirectory(){
        $dir = Di::getInstance()->get(SysConst::APPLICATION_DIR);
        if(empty($dir)){
            $dir = "App";
            Di::getInstance()->set(SysConst::APPLICATION_DIR,$dir);
        }
        $prefix = $dir;
        AutoLoader::getInstance()->addNamespace($prefix,$dir);
    }
}