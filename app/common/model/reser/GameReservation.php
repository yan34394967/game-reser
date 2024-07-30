<?php

namespace app\common\model\reser;

use app\common\model\BaseModel;
use plugin\admin\app\model\Base;
use plugin\admin\app\model\RustUser;

class GameReservation extends BaseModel
{
    /**
     * @var string
     */
    protected $connection = 'reser_mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cc_game_reservation';

    // 关联用户表
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
