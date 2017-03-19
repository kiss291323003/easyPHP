<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:47
 */

namespace Conf;


use Core\AbstractInterface\AbstractEvent;
use Core\Http\Request\Request;
use Core\Http\Response\Response;

class Event extends AbstractEvent
{
    function frameInitialize()
    {
        // TODO: Implement frameInitialize() method.
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

}
