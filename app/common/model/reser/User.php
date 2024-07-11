<?php

namespace app\common\model\reser;

use app\common\model\BaseModel;

class User extends BaseModel
{
    protected $connection = 'reser_mysql';

    protected $table = 'cc_user';
}
