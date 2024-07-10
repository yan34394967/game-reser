<?php

namespace app\common\lib;

use think\facade\Db;

class Str
{
    /**
     * 生成登录所需的token
     * @param $string
     * @return string
     */
    public static function getLoginToken($string)
    {
        $str = md5(uniqid(md5(microtime(true)), true)); // 生成一个不会重复的值
        $token = sha1($str . $string); // 加密
        return $token;
    }

    /**
     * 双层md5加密
     * @param $val
     * @return string
     */
    public static function imd5($val)
    {
        return md5(substr(md5($val), 4, 10) . substr(md5($val), 20, 10));
    }

    /**
     * 生成推荐码
     * @param int $len
     * @return string
     */
    public static function getSpread($len = 10, $type = true)
    {
        $info = "";
        if ($type) {
            $pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $pattern = '1234567890';
        }
        for($i = 0; $i < $len; $i++) {
            $info .= $pattern[mt_rand(0, strlen($pattern)-1)];    // 生成php随机数
        }
        return $info;
    }

    /**
     * 生成用户uuid
     * @param int $len
     * @return int
     * @throws \think\db\exception\DbException
     */
    public static function getUuid()
    {
        $count = Db::table('cc_user')->count();
        if ($count < 999000) {
            return mt_rand(100000, 999999);
        } else if ($count < 9999000) {
            return mt_rand(1000000, 9999999);
        } else if ($count < 99990000) {
            return mt_rand(10000000, 99999999);
        } else if ($count < 999900000) {
            return mt_rand(100000000, 999999999);
        } else {
            return mt_rand(1000000000, 9999999999);
        }
    }

    /**
     * 生成唯一订单id
     * @param bool $type
     * @return int|string
     * @throws \think\Exception
     */
    public static function getOrderId($type = true)
    {
        $workId = rand(1, 1023);
        $orderId = Snowflake::getInstance()->setWorkId($workId)->nextId();
        if ($type) {
            return date('YmdHis') . $orderId;
        }
        return $orderId;
    }

    /**
     * 生成购买矿机订单号
     * @return string
     */
    public static function getMiningOrderId($num = 6)
    {
        return date('YmdHis', time()) . self::getSpread($num, false);
    }

    /**
     * 截取文件名 不带域名
     * @param $path // 文件名地址
     * @param $pathType // ture为拼接域名, false为删除前缀域名
     * @return mixed|string
     */
    public static function getFilePath($path = '', $pathType = false)
    {
        $url = config('extra.env.upload_url');
        if (! $pathType) {
            if (! empty($path)) {
                if (substr($path, 0, strlen($url)) == $url) {
                    $path = substr($path, strlen($url));
                }
            }
        } else {
            if (substr($path, 0, strlen($url)) != $url) {
                $path = $url . $path;
            }
        }
        return $path;
    }

    /*
     * html模板
     */
    public static function getHtml($title, $content)
    {
        $html = <<<EOF
<html lang="en">

<link>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="" type="image/x-icon"></link>
  <title>{$title}</title>
</head>

<body>
{$content}
</body>

</html>
EOF;
        return $html;
    }
}
