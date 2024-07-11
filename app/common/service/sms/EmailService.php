<?php

namespace app\common\service\sms;

use app\common\lib\RedisCache;
use app\common\lib\Str;
use app\common\service\BaseService;
use think\facade\Db;

class EmailService extends BaseService
{
    protected static $sendMail;
    public function __construct()
    {
        self::$sendMail = new \app\common\lib\sms\SendMail();
    }

    /*
     * 发送邮件
     * @param $email
     * @return false|int
     * @throws \think\Exception
     */
    public static function sendMail($email, $html, $code, $userId, $subject='')
    {
        // 检测发送时间
        parent::checkSmsCode($email);

        // 发送验证码
        $sendMail = self::$sendMail->sendMail($email, $html, $subject);
        if ($sendMail['code'] == 0) {
            self::errorBus($sendMail['msg']);
        }
        if ($sendMail['code']) {
            // 设置缓存
            $smsCode = parent::setSmsCode($email, $code);
            if ($smsCode) {
//                // 足迹日志
//                event('Footmark', [
//                    'user_id' => $userId,
//                    'email' => $email,
//                    'type' => 15,
//                ]);
                return $code;
            }
        }
        return $sendMail['code'];
    }

    /**
     * 找回密码发送邮件
     * @param $email
     * @param $username
     * @param $userId
     * @return mixed
     */
    public static function resetPasswordMail($email, $username, $userId)
    {
        $code = Str::getSpread(6);
        $html = self::$sendMail->html($username, $code);
        $subject = '【Ruby】Reset Password Verification';
        return self::sendMail($email, $html, $code, $userId, $subject);
    }

    /**
     * 注册发送邮件验证码
     * @param $email
     * @param $userId
     * @return mixed
     */
    public static function regMail($email, $userId)
    {
        $code = Str::getSpread(6, false);
        // 获取配置
        $option = Str::getOption('emailCodeTemplate')['option_value'];

        $html = self::$sendMail->getHtml($code, $option);
        return self::sendMail($email, $html, $code, $userId, $option['title']);
    }

    /**
     * 发送邮件
     * @param $email
     * @param $option
     * @param $dowmload
     * @return mixed
     */
    public static function sendFromMail($email, $option, $dowmload=false)
    {
//        Db::startTrans();
//        $checkCode = parent::checkCode($email, $option['code']);
//        if (!$checkCode) {
//            Db::rollback();
//            return false;
//        }

        if ($dowmload) {
            $code = "<a href='".$option['code']."' target='_blank'>下载文件</a>";
        } else {
            $code = $option['code'];
        }

        $html = self::$sendMail->getHtml($code, $option);
        $res = self::sendMail($email, $html, $option['code'], 0, $option['title']);

//        if ($res != $code) {
//            Db::rollback();
//            return false;
//        }
//        Db::commit();
        return $res;
    }
}
