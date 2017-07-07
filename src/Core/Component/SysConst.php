<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/2/6
 * Time: 下午5:36
 */

namespace Core\Component;


class SysConst
{
    /*
     * DI开头为依赖注入键值名称
     */
    const DI_ERROR_HANDLER = 'DI_ERROR_HANDLER';
    const DI_LOGGER_WRITER = 'DI_LOGGER_WRITER';
    const DI_EXCEPTION_HANDLER = 'DI_EXCEPTION_HANDLER';
    const DI_SESSION_HANDLER = 'DI_SESSION_HANDLER';

    const CONTROLLER_MAX_DEPTH = 'CONTROLLER_MAX_DEPTH';
    const APPLICATION_DIR = 'APPLICATION_DIR';//定义应用加载目录（以便支持对域名部署不同应用）

    const SHARE_MEMORY_FILE = 'SHARE_MEMORY_FILE';
    const TEMP_DIRECTORY = 'TEMP_DIRECTORY';

    const VERSION_CONTROL = 'VERSION_CONTROL';
}