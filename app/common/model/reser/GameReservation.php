<?php

namespace app\common\model\reser;

use plugin\admin\app\model\Base;
use plugin\admin\app\model\RustUser;

class GameReservation extends Base
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.reser_mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cc_game_reservation';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $dates = ['create_time', 'update_time'];

    // 设置时间格式
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    // 关联用户表
    public function user()
    {
        return $this->belongsTo(RustUser::class, 'user_id');
    }

}
