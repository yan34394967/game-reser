<?php

namespace app\common\lib;

use support\exception\BusinessException;
use support\Redis;

class RedisCache
{
    /**
     * 获取缓存
     * @param $key
     * @param int $num
     * @return mixed
     */
    public static function getCache($key, $num = 0)
    {
        Redis::select($num);
        $val = Redis::get($key);
        if (empty($val)) {
            return [];
        }
        return json_decode($val, true);
    }

    /**
     * 设置缓存
     * @param $key
     * @param array $data
     * @param int $expiresTime
     * @param int $num
     * @return bool|int
     */
    public static function setCache($key, $data = [], $expiresTime = 0, $num = 0)
    {
        Redis::select($num);
        if ([] === $data || is_null($data)) {
            // 删除缓存
            return Redis::del($key);
        }
        $res = Redis::set($key, json_encode($data));
        if ($expiresTime > 0) {
            Redis::expire($key, $expiresTime);
        }
        return $res;
    }

    /**
     * 未找到方法时调用
     * @param $name
     * @param $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
//        // 例子
//        Redis::zscore([
//            [$postsKey, $posts['id']],
//            $num
//        ]);
        Redis::select($args[0][1]);
        return Redis::$name(...$args[0][0]);
    }

    /**
     * 批量删除键
     * @param $key
     * @param $num
     * @return bool
     * @throws \RedisException
     */
    public static function delKeys($key, $num = 0)
    {
        Redis::select($num);
        $cacheKeys = Redis::keys($key);
        if (! empty($cacheKeys)) {
            foreach ($cacheKeys as $cacheKey) {
                Redis::del($cacheKey);
            }
        }
        return true;
    }

    /**
     * 缓存数据 有直接返回,没有创建
     * @param $key
     * @param $callback
     * @param $num
     * @return array|mixed
     * @throws \RedisException
     */
    public static function dataCache($key, $callback, $num = 2)
    {
        $cache = self::getCache($key, $num);
        if ($cache) {
            return $cache;
        }
        $obj = $callback();
        if ($obj != null && ! empty($obj)) {
            if (!is_array($obj)) {
                $obj = $obj->toArray();
            }
            self::setCache($key, $obj, config('extra.redis.data_expires_time'), $num);
        }
        return self::getCache($key, $num) ?? [];
    }

    /**
     * 有序列表,排名
     * @param $key
     * @param $value
     * @param $score
     * @param $num
     * @param $expire
     * @return int|\Redis
     * @throws \RedisException
     */
    public static function zAddCache($key, $value, $score = 0, $num = 0, $expire = 0)
    {
        Redis::select($num);
        $res = Redis::zAdd($key, $score, $value);
        if ($expire > 0) {
            Redis::expire($key, $expire);
        }
        return $res;
    }

}
