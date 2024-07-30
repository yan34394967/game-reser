<?php

namespace app\common\service\reser;

use app\common\lib\RedisCache;
use app\common\model\reser\GameReser;
use app\common\model\reser\GameReservation;
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

        $hasReser = GameReser::getFindData(['name' => $name],'id,status,update_time');
        if ($hasReser) {
            if ($hasReser['status'] == 1) {
                $date = date('Y/m/d', $hasReser['update_time']);
                $time = date('H:i', $hasReser['update_time']);
                return self::errorBus("You have completed the reservation on $date at $time");
            } else {
                $hasReser->status = 1;
                $hasReser->update_time = time();
                $res = $hasReser->save();
            }
        } else {
            $res = GameReser::create([
                'name' => $name,
                'type' => $type,
                'status' => 1
            ]);
        }
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 预约记录
     * @param $page
     * @param $limit
     * @return GameReser[]|array|\think\Collection|\think\db\Query[]|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function reserLog($page=1, $limit=10)
    {
        $game_reservation_count = GameReservation::count();
        $tank_game_reser_count = GameReser::count();
//        $res['count'] = $game_reservation_count + $tank_game_reser_count;
        $res['count'] = GameReser::count();
        $res['lists'] = GameReser::field('id,name,create_time')
            ->where([['update_time', '<', time()]])
            ->order(['update_time' => 'desc'])
            ->page($page, $limit)
            ->select()->each(function ($item) {
                $exp = explode('@', $item['name']);
                if (count($exp) == 2) {
                    $name = substr_replace($exp[0], '****', 2, -2).'@'. $exp[1];
                } else {
                    $name = substr_replace($exp[0], '****', 2, -2);
                }
                $item['name'] = $name;
            });
        return $res;
    }
}
