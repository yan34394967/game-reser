<?php

namespace app\common\service\sms;

use app\common\service\BaseService;
use support\Log;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

class TxSmsService extends BaseService
{
    protected static $secretId;
    protected static $secretKey;
    protected static $templateId;
    protected static $sign;

    public function __construct()
    {
        self::$secretId = config('env.tx_sms.secretId');
        self::$secretKey = config('env.tx_sms.secretKey');


        // 腾讯云短信接口配置
//        $appId = 'your_app_id';
//        $appKey = 'your_app_key';
//        $templateId = 'your_template_id';
//        $sign = 'your_sign_name';
    }

    public static function sendSms($mobile, $user)
    {
        try {
            // 实例化一个认证对象，入参需要传入腾讯云账户 SecretId 和 SecretKey，此处还需注意密钥对的保密
            // 代码泄露可能会导致 SecretId 和 SecretKey 泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议采用更安全的方式来使用密钥，请参见：https://cloud.tencent.com/document/product/1278/85305
            // 密钥可前往官网控制台 https://console.cloud.tencent.com/cam/capi 进行获取
            $cred = new Credential(self::$secretId, self::$secretKey);
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setReqTimeout(60);
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");

            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法
            $clientProfile->setHttpProfile($httpProfile);

            // 第二个参数是地域信息，可以直接填写字符串ap-guangzhou，支持的地域列表参考 https://cloud.tencent.com/document/api/382/52071#.E5.9C.B0.E5.9F.9F.E5.88.97.E8.A1.A8
            $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);

            // 实例化一个 sms 发送短信请求对象,每个接口都会对应一个request对象。
            $req = new SendSmsRequest();
            // 应用 ID 可前往 [短信控制台](https://console.cloud.tencent.com/smsv2/app-manage) 查看
            $req->SmsSdkAppId = "1400923260";
            /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名 */
            // 签名信息可前往 [国内短信](https://console.cloud.tencent.com/smsv2/csms-sign) 或 [国际/港澳台短信](https://console.cloud.tencent.com/smsv2/isms-sign) 的签名管理查看
            $req->SignName = "洛阳蓝景科技有限公司";
            /* 模板 ID: 必须填写已审核通过的模板 ID */
            // 模板 ID 可前往 [国内短信](https://console.cloud.tencent.com/smsv2/csms-template) 或 [国际/港澳台短信](https://console.cloud.tencent.com/smsv2/isms-template) 的正文模板管理查看
            $req->TemplateId = "2208652";
            /* 模板参数: 模板参数的个数需要与 TemplateId 对应模板的变量个数保持一致，若无模板参数，则设置为空*/
            $req->TemplateParamSet = array();
            /* 下发手机号码，采用 E.164 标准，+[国家或地区码][手机号]
             * 示例如：+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
            $req->PhoneNumberSet = array("+86".$mobile);
            /* 用户的 session 内容（无需要可忽略）: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
            $req->SessionContext = "";
            /* 短信码号扩展号（无需要可忽略）: 默认未开通，如需开通请联系 [腾讯云短信小助手] */
            $req->ExtendCode = "";
            /* 国内短信无需填写该项；国际/港澳台短信已申请独立 SenderId 需要填写该字段，默认使用公共 SenderId，无需填写该字段。注：月度使用量达到指定量级可申请独立 SenderId 使用，详情请联系 [腾讯云短信小助手](https://cloud.tencent.com/document/product/382/3773#.E6.8A.80.E6.9C.AF.E4.BA.A4.E6.B5.81)。*/
            $req->SenderId = "";

            // 通过client对象调用SendSms方法发起请求。注意请求方法名与请求对象是对应的
            // 返回的resp是一个SendSmsResponse类的实例，与请求对象对应
            $resp = $client->SendSms($req);

            print_r($resp->toJsonString());
        } catch(TencentCloudSDKException $e) {
            Log::error('发送失败-error:'.$e->getMessage().$e->getFile().$e->getLine());
            return false;
        }
    }



    // 发送语音验证码
    /*function sendVoiceCode($phoneNumber)
    {
        //        global $appId, $appKey, $templateId, $sign;

        $client = new Client([
            'base_uri' => 'https://sms.tencentcloudapi.com',
            'timeout' => 5.0,
        ]);

        try {
            $response = $client->request('POST', '/', [
                'json' => [
                    'Action' => 'SendSms',
                    'Version' => '2021-01-11',
                    'Region' => 'ap-guangzhou',
                    'PhoneNumberSet' => [$phoneNumber],
                    'TemplateID' => $templateId,
                    'TemplateParamSet' => ['your_verification_code'],
                    'SignName' => $sign,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth' => [$appId, $appKey],
            ]);

            $result = json_decode($response->getBody(), true);
            if ($result['Response']['Error']['Code'] === 'OK') {
                echo '语音验证码发送成功';
            } else {
                echo '语音验证码发送失败：' . $result['Response']['Error']['Message'];
            }
        } catch (\Exception $e) {
            echo '请求出错：' . $e->getMessage();
        }
    }*/
}






