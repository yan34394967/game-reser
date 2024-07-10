<?php

namespace App\middleware;

use app\common\lib\RedisCache;
use app\common\lib\Timer;
use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class AuthCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        // 验证用户登录
        $token = $request->header('Authori-zation');

        $key = config('extra.redis.login_token_pre') . $token;
        $userCache = RedisCache::getCache($key, 1);
        // 不需要登录的方法
        $notNeed = [];
        if (! in_array($request->action, $notNeed) || !empty($userCache)) {
            if (!$userCache) {
                throw new BusinessException('用户未登录', config('extra.status.not_login'));
            }
            if ($userCache['status'] != 1) {
                throw new BusinessException('账号异常,请联系管理员', config('extra.status.error_login'));
            }
            RedisCache::setCache($key, $userCache, Timer::loginExpiresTime(), 1);
            // 控制器传参
            $request->user = $userCache;
            $request->userId = $userCache['id'];
        }
        $request->token = $token;

        return $handler($request);
    }
}
