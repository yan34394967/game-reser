<?php

namespace app\common\lib;

class Timer
{
    /**
     * 获取当天的时间
     * @return false|int
     */
    public static function getToday()
    {
        return strtotime(date('Y-m-d', time()));
    }

    /**
     * 获取昨天时间
     * @return false|int
     */
    public static function getYestoday()
    {
        return strtotime(date("Y-m-d",strtotime("-1 day")));
    }

    /**
     * 获取当月时间
     * @return false|int
     */
    public static function getMonth()
    {
        return strtotime(date('Y-m', time()));
    }

    /**
     * 用户登录信息有效期
     * @param int $type
     * @return float|int
     */
    public static function loginExpiresTime($type = 1)
    {
        switch ($type) {
            case 1:
                $day = 7;
                break;
            case 2:
                $day = 30;
                break;
        }
        return $day * 24 * 3600;
    }

    /**
     * 当天剩余时间,到第二天00:00:00
     * @return int
     */
    public static function taskExpire()
    {
        return strtotime(date('Y-m-d', strtotime('+1 day')).' 00:00:00') - time();
    }

    /*
     * 体验时间
     */
    public static function experienceTime()
    {
        $day = 21;
        return time() + ($day * 24 * 3600);
    }

    /**
     * 获取14位毫秒时间戳
     * @return int
     */
    public static function getMicrotime()
    {
        return intval(microtime(true)*10000);
    }
}
