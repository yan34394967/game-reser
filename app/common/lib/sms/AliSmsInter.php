<?php

namespace app\common\lib\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\common\lib\Str;
use think\facade\Log;

/**
 * 阿里云 国际短信
 * Class AliSms
 * @package app\common\lib\sms
 */
class AliSmsInter
{
    public function __construct()
    {
        // 设置全局客户端
        AlibabaCloud::accessKeyClient(config('extra.aliyun.inter.access_key_id'), config('extra.aliyun.inter.access_secret'))
            ->regionId(config('extra.aliyun.inter.region_id'))
            ->asDefaultClient();
    }

    /**
     * 阿里云发送短信验证码的场景
     * @return bool
     */
    public function sendInterCode($phone, $code)
    {
        // 获取配置
        $option = Str::getOption('phoneCodeTemplate');
        $msg = $option['option_value']['content'].$code.", please do not disclose to others";
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2018-05-01')
                ->action('SendMessageToGlobe')
                ->method('POST')
                ->host(config('extra.aliyun.inter.host'))
                ->options([
                    'query' => [
                        "To" => $phone,
//                        "From" => "1234567890",//自定义短信来源（也就是发送方名称）
                        "Message" => $msg,
                    ],
                ])
                ->request();
            Log::info("alisms-sendCode-result-{$phone}-".json_encode($result->toArray())); // 记录日志
//            print_r($result->toArray());
        } catch (ClientException $e) {
            Log::error("alisms-sendCode-ClientException-{$phone}-".$e->getErrorMessage());
            return false;
        } catch (ServerException $e) {
            Log::error("alisms-sendCode-ServerException-{$phone}-".$e->getErrorMessage());
            return false;
        }
        if (isset($result['ResponseCode']) && $result['ResponseCode'] == 'OK') {
            return true;
        }
        return false;
    }

}
