<?php

namespace plugin\admin\app\model;

class BaseModel extends Base
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    /**
     * 模型日期字段的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';

    protected $casts = [
        'create_time' => 'datetime:Y-m-d',
        'update_time' => 'datetime:Y-m-d',
    ];
}
