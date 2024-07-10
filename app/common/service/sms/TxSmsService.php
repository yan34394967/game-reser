<?php

namespace app\common\service\sms;

use app\common\service\BaseService;
use GuzzleHttpExceptionClientException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Cvm\V20170312\CvmClient;
use TencentCloud\Cvm\V20170312\Models\DescribeRegionsRequest;

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
            $httpProfile->setEndpoint("cvm.tencentcloudapi.com");

            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            // 实例化要请求产品的client对象,clientProfile是可选的
            $client = new CvmClient($cred, "", $clientProfile);

            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new DescribeRegionsRequest();

            $params = array(
                "PhoneNumberSet" => array( "+86".$mobile ),
                "TemplateID" => "2208652",
                "SmsSdkAppid" => "1400923260",
                "Sign" => "坦克对决",
                "SessionContext" => "test"
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个DescribeRegionsResponse的实例，与请求对象对应
            $resp = $client->DescribeRegions($req);

            // 输出json格式的字符串回包
            print_r($resp->toJsonString());
        }
        catch(TencentCloudSDKException $e) {
            echo $e;
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






