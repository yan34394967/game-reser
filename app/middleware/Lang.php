<?php

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Lang implements MiddlewareInterface
{
    /**
     * 多语言设置,每次请求切换语言
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler) : Response
    {
        // 语言类型
        $lang = request()->header('Reser-lang', 'zh_CN');
        locale($lang);
        // 设备类型
        $device = request()->header('Device-type', 'web');
        $request->device = $device;
        return $handler($request);
    }
}
