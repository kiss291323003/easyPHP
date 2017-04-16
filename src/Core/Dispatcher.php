<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午6:45
 */

namespace Core;


use Conf\Event;
use Core\AbstractInterface\AbstractController;
use Core\AbstractInterface\AbstractRouter;
use Core\Component\Di;
use Core\Component\SysConst;
use Core\Http\Request\Request;
use Core\Http\Response\Response;
use Core\Component\SuperClosure;
use Core\Http\Status;
use FastRoute\Dispatcher\GroupCountBased;
use Core\Component\RouteCollector;

class Dispatcher
{
    protected static $instance;
    private $appDirectory;
    /*
     * Core Instance is a singleTon in a request lifecycle
     * @return Dispatcher instance
     */
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }
    function __construct()
    {
        $this->appDirectory = Di::getInstance()->get(SysConst::APPLICATION_DIR);
    }

    public function dispatch(Request $request,Response $response){
        if($response->isEndResponse()){
            return;
        }
        $pathInfo = UrlParser::parser($request);
        $routeInfo = $this->fastRouter($pathInfo,Request::getInstance()->getServer("REQUEST_METHOD"));
        if($routeInfo !== false){
            switch ($routeInfo[0]) {
                case \FastRoute\Dispatcher::NOT_FOUND:
                    // ... 404 NdoDispatcherot Found
                    break;
                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    $response->sendHttpStatus(Status::CODE_METHOD_NOT_ALLOWED);
                    break;
                case \FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    if(is_callable($handler)){
                        call_user_func_array($handler,$vars);
                    }
                    break;
            }
        }
        if($response->isEndResponse()){
            return;
        }
        //去除为fastRouter预留的左边斜杠
        $pathInfo = ltrim($pathInfo,"/");
        $list = explode("/",$pathInfo);
        $controllerNameSpacePrefix = "{$this->appDirectory}\\Controller";
        $actionName = null;
        $finalClass = null;
        $controlMaxDepth = Di::getInstance()->get(SysConst::CONTROLLER_MAX_DEPTH);
        if(intval($controlMaxDepth) <= 0){
            $controlMaxDepth = 3;
        }
        $maxDepth = count($list) < $controlMaxDepth ? count($list) : $controlMaxDepth;
        $isIndexController = 0;
        while ($maxDepth > 0){
            $className = '';
            for ($i=0 ;$i<$maxDepth;$i++){
                $className = $className."\\".ucfirst($list[$i]);//为一级控制器Index服务
            }
            if(class_exists($controllerNameSpacePrefix.$className)){
                //尝试获取该class后的actionName
                $actionName = isset($list[$i]) ? $list[$i] : '';
                $finalClass = $controllerNameSpacePrefix.$className;
                break;
            }else{
                //尝试搜搜index控制器
                $temp = $className."\\Index";
                if(class_exists($controllerNameSpacePrefix.$temp)){
                    $finalClass = $controllerNameSpacePrefix.$temp;
                    //尝试获取该class后的actionName
                    $actionName = isset($list[$i]) ? $list[$i] : null;
                    break;
                }
            }
            $maxDepth--;
        }
        if(empty($finalClass)){
            //若无法匹配完整控制器   搜搜Index控制器是否存在
            $finalClass = $controllerNameSpacePrefix."\\Index";
            $isIndexController = 1;
        }
        if(class_exists($finalClass)){
            if($isIndexController){
                $actionName = isset($list[0]) ? $list[0] : '';
            }
            $actionName = $actionName ? $actionName : "index";
            $controller = new $finalClass;
            if($controller instanceof AbstractController){
                Event::getInstance()->onDispatcher($request,$response,$finalClass,$actionName);
                //预防在进控制器之前已经被拦截处理
                if(!$response->isEndResponse()){
                    $controller->actionName($actionName);
                    $controller->onRequest($actionName);
                    //同上
                    if(!$response->isEndResponse()){
                        $controller->$actionName();
                        $controller->afterResponse();
                    }
                }
            }else{
                Response::getInstance()->sendHttpStatus(Status::CODE_NOT_FOUND);
                trigger_error("controller {$finalClass} is not a instance of AbstractController");
            }
        }else{
            Response::getInstance()->sendHttpStatus(Status::CODE_NOT_FOUND);
            trigger_error("default controller Index not implement");
        }
    }
    private function fastRouter($pathInfo,$requestMethod){
        try{
            /*
                 * if exit Router class in App directory
            */
            $ref = new \ReflectionClass("{$this->appDirectory}\\Router");
            $router = $ref->newInstance();
            if($router instanceof AbstractRouter){
                $is = $router->isCache();
                if($is){
                    $is = $is.".{$this->appDirectory}";
                    if(file_exists($is)){
                        $dispatcherData = require_once "{$is}";
                        $dispatcherData = unserialize($dispatcherData);
                    }else{
                        $dispatcherData =  RouteCollector::getInstance()->getData();
                        $cache =  $dispatcherData;
                        /*
                         * to support closure
                         */
                        array_walk_recursive($cache,function(&$item,$key){
                            if($item instanceof \Closure){
                                $item = new SuperClosure($item);
                            }
                        });
                        file_put_contents(
                            $is,
                            "<?php return '" . serialize($cache) . "';"
                        );
                    }
                    $fastRouterDispatcher = new GroupCountBased($dispatcherData);
                    return $fastRouterDispatcher->dispatch($requestMethod,$pathInfo);
                }else{
                    $fastRouterDispatcher = new GroupCountBased(RouteCollector::getInstance()->getData());
                    return $fastRouterDispatcher->dispatch($requestMethod,$pathInfo);
                }
            }
        }catch (\Exception $e){

        }
        return false;
    }
}