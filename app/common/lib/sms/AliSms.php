<?php

namespace app\common\lib\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use support\Log;

/**
 * 阿里云 国内短信
 * Class AliSms
 * @package app\common\lib\sms
 */
class AliSms
{
    public function __construct()
    {
        // 设置全局客户端
        AlibabaCloud::accessKeyClient(config('extra.aliyun.message.access_key_id'), config('extra.aliyun.message.access_secret'))
            ->regionId(config('extra.aliyun.message.region_id'))
            ->asDefaultClient();
    }

    /**
     * 阿里云发送短信验证码的场景
     * @return bool
     */
    public function sendCode($phone, $code)
    {
        $templateParam = ['code' => $code];
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host(config('extra.aliyun.message.host'))
                ->options([
                    'query' => [
                        'RegionId' => config('extra.aliyun.message.region_id'),
                        'PhoneNumbers' => $phone,
                        'SignName' => config('extra.aliyun.message.sign_name'),
                        'TemplateCode' => config('extra.aliyun.message.template_code'),
                        'TemplateParam' => json_encode($templateParam),
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
        if (isset($result['Code']) && $result['Code'] == 'OK') {
            return true;
        }
        return false;
    }

}
