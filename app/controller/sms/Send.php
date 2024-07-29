<?php

namespace app\controller\sms;

use app\common\controller\ApiController;
use app\common\lib\Str;
use app\common\service\sms\EmailService;
use app\common\service\sms\TxSmsService;
use app\validate\send\SendValidate;

class Send extends ApiController
{
    /**
     * 发送邮件
     * @param EmailService $emailBus
     * @param SendValidate $sendValidate
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function sendMail(EmailService $emailBus, SendValidate $sendValidate)
    {
        $email = request()->post('email', '');
        $data = [
            'email' => $email
        ];
        $sendValidate->goCheck('sendMail', $data);

        // 发送邮件业务
        $option = [
            'code' => Str::getSpread(6, false),
            'title' => '【Reservition】你正在申请预约',
            'content' => '你的验证码为：',
        ];
        $res = $emailBus->sendFromMail($email, $option);
        if ($res) {
            return self::success([], trans('Send a success'));
        }
        return self::error(trans('Send failure'));
    }

    /**
     * 发送手机验证码
     * @param SendValidate $sendValidate
     * @param TxSmsService $txSmsService
     * @return \support\Response
     * @throws \Exception
     */
    public function sendMobile(SendValidate $sendValidate, TxSmsService $txSmsService)
    {
        $user = request()->user;
        $mobile = request()->post('mobile', '');
        $data = [
            'mobile' => $mobile
        ];
        $sendValidate->goCheck('sendMobile', $data);

        $code = Str::getSpread(6, false);
        $res = $txSmsService::sendSms($mobile, $code);
        if ($res) {
            return self::success([], trans('Send a success'));
        }
        return self::error(trans('Send failure'));
    }
}
