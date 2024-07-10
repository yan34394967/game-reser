<?php

namespace app\common\lib;

class Walletapi
{
    protected  $host = '';
    protected  $appId = '';
    protected  $appSecret = '';

    public function __construct()
    {
        $this->host = config('extra.wallet.http_api', '');
        $this->appId = config('extra.wallet.app_id', '');
        $this->appSecret = config('extra.wallet.app_secret', '');
    }

    /**
     * 创建地址
     *
     * bnb  0xb6a351c69729988c21fB982950849c0D6374af9B   622
     * usdt 0xb6a351c69729988c21fB982950849c0D6374af9B   623
     *
     * @ApiTitle    (创建地址)
     * @ApiParams   (name="userId", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="rpc_symbolId", type="integer", required=true, description="rpc公链币种id")
     */
    public function createWalletapiAddress($arguments)
    {
        extract($arguments);

        $url = $this->host . '/hdWallets/api/account/createAccount';

        $data = [
            'uid' => $userId,
            'symbolId' => $rpc_symbolId,
        ];

        $now_time = time()+2;

        $header_data = [
            'appId:' . $this->appId,
            'timestamp:' . $now_time,
            "content-type: application/json;charset=UTF-8",
        ];

        $sign = $this->sign(array_merge(['appId' => $this->appId, 'timestamp' => $now_time], $data));

        $header_data[] = 'sign:' . $sign;

        $res = self::curl_post($url, $data, 15, $header_data, 'json');

        if ($res['code'] == 1) {
            $return_data = json_decode($res['data'], true);
            if (!isset($return_data['success']) || !$return_data['success']) {
                $res = [
                    'code' => 0,
                    'msg' => isset($return_data['message']) ? $return_data['message'] : 'undefined error',
                ];
            } else {
                $res = [
                    'code' => 1,
                    'data' => [
                        'address' => $return_data['result']['address'],
                        'rpc_walletId' => isset($return_data['result']['walletId  ']) ? $return_data['result']['walletId  '] : $return_data['result']['walletId'],
                        'tag' => $return_data['result']['addressTag'],
                    ],
                ];
            }
        } else {
            $res = [
                'code' => 0,
                'msg' => $res['data'],
            ];
        }

        return $res;
    }

    //

    /**
     * 创建交易
     *
     * @ApiTitle    (创建交易)
     * @ApiParams   (name="amount", type="float", required=true, description="交易金额")
     * @ApiParams   (name="clientOrderId", type="string", required=true, description="订单号")
     * @ApiParams   (name="toAddress", type="string", required=true, description="转账地址")
     * @ApiParams   (name="walletId", type="string", required=true, description="rpc公链钱包id")
     * @ApiParams   (name="toAddressTag", type="string", required=false, description="tag")
     */
    public function walletapiTransfer($arguments)
    {
        extract($arguments);

        $url = $this->host . '/hdWallets/api/account/transfer';

        $now_time = time()+2;

        $header_data = [
            'appId:' . $this->appId,
            'timestamp:' . $now_time,
            "content-type: application/json;charset=UTF-8",
        ];

        $data = [
            'amount' => $amount,
            'clientOrderId' => $clientOrderId,
            'toAddress' => $toAddress,
            'walletId' => $walletId,
            'toAddressTag' => isset($toAddressTag) ? $toAddressTag : '',
        ];

        $sign = $this->sign(array_merge(['appId' => $this->appId, 'timestamp' => $now_time], $data));

        $header_data[] = 'sign:' . $sign;

        $res = self::curl_post($url, $data, 15, $header_data, 'json');

        if ($res['code'] == 1) {
            $return_data = json_decode($res['data'], true);
            if (!isset($return_data['success']) || !$return_data['success']) {
                $res = [
                    'code' => 0,
                    'msg' => isset($return_data['message']) ? $return_data['message'] : 'undefined error',
                ];
            } else {
                $res = [
                    'code' => 1,
                    'data' => [
                        'transactionId' => $return_data['result']['transactionId'],
                    ],
                ];
            }
        } else {
            $res = [
                'code' => 0,
                'msg' => $res['data'],
            ];
        }

        return $res;
    }

    public  function sign($arr)
    {

        ksort($arr);

        $sign = '';

        foreach ($arr as $k => $v) {
            $sign .= $k . '=' . $v . '&';
        }

        return md5(rtrim($sign, '&') . $this->appSecret);
    }

    private static function curl_post($url, $post_data = array(), $timeout = 15, $header = [], $data_type = "")
    {
        $header = empty($header) ? [] : $header;
        //支持json数据数据提交
        if ($data_type == 'json') {
            $post_string = json_encode($post_data);
        } elseif ($data_type == 'array') {
            $post_string = $post_data;
        } elseif (is_array($post_data)) {
            $post_string = http_build_query($post_data, '', '&');
        }

        $ch = curl_init();    // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url);     // 要访问的地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($ch, CURLOPT_POST, true); // 发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);     // Post提交的数据包
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);     // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 获取的信息以文件流的形式返回
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头

        $code = 1; //执行成功
        $data = curl_exec($ch);
        //捕抓异常
        if (curl_errno($ch)) {
            $code = 0; //执行异常
            $data = curl_error($ch);
        }
        curl_close($ch);

        return ['code' => $code, 'data' => $data];
    }
}
