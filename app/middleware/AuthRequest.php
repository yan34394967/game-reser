<?php

namespace App\middleware;

use app\common\lib\RedisCache;
use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AuthRequest implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // echo '这里是请求穿越阶段，也就是请求处理前' . PHP_EOL;
        // 前置中间件
        $param  = $request->post();
        $token = $request->header('Authori-zation');
        $pathinfo = $request->path();
        if (! empty($param) && ! empty($token)) {
            $key = sha1($token . md5($pathinfo) .md5(json_encode($param)));
            $get = RedisCache::getCache($key);
            if ($get == $key) {
                throw new BusinessException(trans('⚠ Try again later'));
            } else {
                RedisCache::setCache($key, $key, 600);
            }
        }

        $response = $handler($request); // 继续向洋葱芯穿越，直至执行控制器得到响应

//        echo '这里是响应穿出阶段，也就是请求处理后' . PHP_EOL;
        // 后置中间件
        if (! empty($param) && ! empty($token)) {
            RedisCache::setCache($key, null);
        }

        return $response;
    }
}
