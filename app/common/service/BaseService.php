<?php

namespace app\common\service;

use app\common\lib\RedisCache;
use app\common\lib\Timer;
use support\exception\BusinessException;

class BaseService
{
    /**
     * 返回错误信息
     * @param $msg
     * @param int $status
     * @throws \think\Exception
     */
    public static function errorBus($msg, $status = 0)
    {
        throw new BusinessException($msg, $status);
    }

    /**
     * 查询单条数据
     * @param $where
     * @param bool $field
     * @param string $order
     * @return false
     */
    public static function getFindData($where, $field = true, $order = 'id desc')
    {
        if (empty($where) && ! is_array($where)) {
            return false;
        }
        $res = self::$model::field($field)->where($where);
        if ($order) {
            $res->order($order);
        }
        return $res->find();
    }

    /**
     * 查询多条数据
     * @param $where
     * @param bool $field
     * @param int $page
     * @param int $num
     * @param string[] $order
     * @return mixed
     */
    public static function getDataByWhere($where, $field = true, $page = 0, $num = 10, $order = ['id' => 'desc'])
    {
        $res = self::$model::field($field);
        if (! empty($where) && is_array($where)) {
            $res->where($where);
        }
        if ($page > 0) {
            $res->page($page, $num);
        }
        return $res->order($order)->select();
    }

    // 检测验证码是否过期
    public static function checkSmsCode($name)
    {
        // 邮箱验证码
        $keyExpire = config('extra.redis.sms_pre_expire').$name;
        $mailCode = RedisCache::getCache($keyExpire);
        if (! empty($mailCode)) {
            self::errorBus(trans("Don't send too often"));
        }
    }

    // 设置验证码缓存
    public static function setSmsCode($name, $code)
    {
        $key = config('extra.redis.sms_pre').$name;
        $expires_time = config('extra.redis.mail_expires_time');
        $keyExpire = config('extra.redis.sms_pre_expire').$name;
        $res = RedisCache::setCache($key, $code, $expires_time);
        $resExpire = RedisCache::setCache($keyExpire, $code, 60);
        if ($res && $resExpire) {
            return true;
        }
        return false;
    }

    /**
     * 检测验证码发送数量
     * @param $name
     * @param $code
     * @return void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function checkCode($name,$code)
    {
        $codesModel = new \app\common\model\config\Codes();
        $dayTime = Timer::getToday();
        $hourTime = Timer::getHour();
        $time = time();
        $hourMax = 10;
        $dayMax = 50;
        $hasCode = $codesModel::getFindData(['name'=>$name,'day_time'=>$dayTime]);
        $hourData = [
            'times' => [$time],
            'codes' => [$code],
            'num' => 1
        ];
        if (!$hasCode) {
            $res = $codesModel::create([
                'name' => $name,
                'code' => $code,
                'day_time' => $dayTime,
                'new_time' => $time,
                'remark' => [
                    $hourTime => $hourData
                ]
            ]);
        } else {
            // 判断当天发送数量
            $dataDayMax = array_sum(array_column(array_values($hasCode['remark']), 'num'));
            if ($dayMax <= $dataDayMax) {
                self::errorBus(trans('The number of items sent today has exceeded the upper limit'));
            }
            $hasCode->code = $code;
            $hasCode->new_time = $time;
            if (isset($hasCode['remark'][$hourTime])) {
                // 判断一个小时内发送数量
                if ($hasCode['remark'][$hourTime]['codes']['num'] >= $hourMax) {
                    self::errorBus(trans('A maximum of %s messages can be sent in one hour', [$hourMax]));
                }
                array_push($hasCode['remark'][$hourTime]['times'], $time);
                array_push($hasCode['remark'][$hourTime]['codes'], $code);
                $hasCode['remark'][$hourTime]['codes']['num'] += 1;
            } else {
                $pushData = [
                    $hourTime => $hourData
                ];
                array_push($hasCode['remark'], $pushData);
            }
            $data = $hasCode['remark'];
            $hasCode->remark = $data;
            $res = $hasCode->save();
        }
        if (!$res) {
            self::errorBus(trans('operation failed'). ' #checkCode');
        }
        return true;
    }
}
