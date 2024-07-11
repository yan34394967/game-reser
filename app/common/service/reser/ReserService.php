<?php

namespace app\common\service\reser;

use app\common\lib\RedisCache;
use app\common\model\reser\GameReser;
use app\common\service\BaseService;

class ReserService extends BaseService
{
    /**
     * 预约
     * @param $name
     * @param $code
     * @param $type
     * @return GameReser|\think\Model
     * @throws \RedisException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function gameReser($name, $code, $type)
    {
        if (empty($name) || empty($code) || !in_array($type, [1,2])) {
            self::errorBus(trans('Invalid parameters'));
        }
        $key = config('extra.redis.sms_pre').$name;
        $getCode = RedisCache::getCache($key);
        if (!$getCode) {
            self::errorBus(trans('Invalid verification code'));
        }
        if ($getCode != $code) {
            self::errorBus(trans('Verification code error'));
        }

        $hasReser = GameReser::getFindData(['name' => $name],'id');
        if ($hasReser) {
            self::errorBus(trans('Already booked'));
        }
        $res = GameReser::create([
            'name' => $name,
            'type' => $type,
            'status' => 1
        ]);
        return $res;
    }
}
