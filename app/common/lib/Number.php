<?php

namespace app\common\lib;

class Number
{
    /**
     * 精确价格后的小数
     * @param $val
     * @param int $num 几位小数
     * @return string
     */
    public static function floatVal($val, $num = 2)
    {
        $exp = explode('.', $val);
        if (isset($exp[1]) && $exp[1] > 0) {
            $expSub = substr($exp[1], 0, $num);
            $expSub = rtrim($expSub, 0);
            $val = $exp[0] .'.'. $expSub;
        } else {
            $val = $exp[0];
        }
        return $val; // 不四舍五入

//        return round($val, $num);
//        $floatNum = substr(sprintf('%.'.($num+1).'f', $val),0, -1); // 不四舍五入
//        return floatval($floatNum);
    }

    /**
     * 将两个高精度数字相减
     * @param $left_num
     * @param $right_num
     * @param int $scale
     * @return string
     */
    public static function bcsubMath($left_num, $right_num, $scale = 2)
    {
        return bcsub($left_num, $right_num, $scale);
    }

    /**
     * 将两个高精度数字相加
     * @param $left_num
     * @param $right_num
     * @param int $scale
     * @return string
     */
    public static function bcaddMath($left_num, $right_num, $scale = 2)
    {
        return bcadd($left_num, $right_num, $scale);
    }

    /**
     * 将两个高精度数字相比较, 左边大为1,右边大为-1,相等为0
     * @param $left_num
     * @param $right_num
     * @param int $scale
     * @return int
     */
    public static function bccompMath($left_num, $right_num, $scale = 2)
    {
        return bccomp($left_num, $right_num, $scale);
    }

    /**
     * 将两个高精度数字相乘
     * @param $left_num
     * @param $right_num
     * @param $scale
     * @return string
     */
    public static function bcmulMath($left_num, $right_num, $scale = 2)
    {
        return bcmul($left_num, $right_num, $scale);
    }

    /**
     * 将两个高精度数字相除
     * @param $left_num
     * @param $right_num
     * @param $scale
     * @return string
     */
    public static function bcdivMath($left_num, $right_num, $scale = 2)
    {
        return bcdiv($left_num, $right_num, $scale);
    }

    /**
     * 负数转正数, 小数位数多的时候使用, 避免丢失精度
     * @param $val
     * @return mixed|string
     */
    public static function numConvert($val)
    {
        if ($val < 0) {
            $val = substr($val,1);
        }
        return $val;
    }

    public static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
}