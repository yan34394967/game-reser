<?php

namespace app\validate;

use support\exception\BusinessException;
use taoser\Validate;

class Request extends Validate
{
    /**
     * 切面验证接收到的参数
     *
     * @param $scene
     * @param array $data
     * @return array|mixed|null
     * @throws \Exception
     */
    public function goCheck($scene=null, array $data=[])
    {
        if (empty($data)) {
            // 1.接收参数
            if (request()->method() == 'GET') {
                $params = request()->get();
            } else {
                $params = request()->post();
            }
        } else {
            $params = $data;
        }
        // 2.验证参数
        if (!($scene ? $this->scene($scene)->check($params) : $this->check($params))) {
            $exception = is_array($this->error)
                ? implode(';', $this->error) : $this->error;

            throw new BusinessException(trans($exception));
        }
        // 3.成功返回数据
        return $params;
    }
}
