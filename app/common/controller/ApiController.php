<?php

namespace app\common\controller;

use app\common\lib\RedisCache;
use support\exception\BusinessException;
use support\Response;

class ApiController
{
    /**
     * 成功输出
     * @param array $data
     * @param string $msg
     * @param int $status
     * @param int $options
     * @return Response
     */
    public static function success($data = [], $msg = 'OK', $status = 1, $options = JSON_UNESCAPED_UNICODE)
    {
        $data = [
            'code' => $status,
            'msg' => $msg,
            'data' => $data
        ];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
    }

    /**
     * 失败输出
     * @param string $msg
     * @param int $status
     * @param array $data
     * @param int $options
     * @return Response
     */
    public static function error($msg = 'error', $status = 0, $data = [], $options = JSON_UNESCAPED_UNICODE)
    {
        $data = [
            'code' => $status,
            'msg' => $msg,
            'data' => $data
        ];
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
    }

    /**
     * 获取每页分页数
     * @param $limit
     * @return int
     */
    public static function paramNum($limit=10)
    {
        $num = request()->param('limit', $limit, 'intval');
        return $num > 100 ? 100 : $num;
    }

    /**
     * 检测验证码是否正确
     * @param $phone
     * @param $code
     * @return bool
     * @throws \think\Exception
     */
    public function checkPhoneCode($userName, $code)
    {
        $cacheCode = Redis::getCache(config('redis.sms_pre').$userName);
        if (! $cacheCode) {
            throw new BusinessException(trans('Verification code has expired'));
        }
        if ($cacheCode != $code) {
            throw new BusinessException(trans('Verification code error'));
        }
        // 验证过后删除
        RedisCache::setCache(config('redis.sms_pre').$userName, NULL);
        RedisCache::setCache(config('redis.sms_pre_expire').$userName, NULL);
        return true;
    }
}
