<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:47
 */

namespace Conf;


use Core\AbstractInterface\AbstractEvent;
use Core\Component\Spl\SplError;
use Core\Http\Request;
use Core\Http\Response;

class Event extends AbstractEvent
{
    function frameInitialize()
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set("Asia/Shanghai");
    }

    function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        // TODO: Implement onDispatcher() method.
    }

    function afterResponse(Request $request, Response $response)
    {
        // TODO: Implement afterResponse() method.
    }

    function onFatalError(SplError $error, $debugTrace)
    {
        // TODO: Implement onFatalError() method.
    }


}
