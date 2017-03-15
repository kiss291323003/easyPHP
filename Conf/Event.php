<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2017/2/8
 * Time: 10:47
 */

namespace Conf;


use App\Vendor\SysConst;
use Core\AbstractInterface\AbstractEvent;
use Core\Component\Di;
use Core\Http\Request;
use Core\Http\Response;

class Event extends AbstractEvent
{
    function frameInitialize()
    {
        // TODO: Implement frameInitialize() method.
        $this->setDb();
    }

    function onRequest(Request $request, Response $response)
    {
        // TODO: Implement onRequest() method.
    }

    function afterResponse(Request $request, Response $response)
    {
        // TODO: Implement afterResponse() method.
    }

    private function setDb(){
        Di::getInstance()->set(SysConst::DB,\MysqliDb::class,array(
            "127.0.0.1",
            "root",
            "root",
            "sdyc"
        ));

    }
}