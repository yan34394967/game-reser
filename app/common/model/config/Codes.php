<?php

namespace app\common\model\config;

use app\common\model\BaseModel;

class Codes extends BaseModel
{
    // 设置json类型字段
    protected $json = ['remark'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
}
