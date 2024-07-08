<?php

namespace plugin\admin\app\model;

use DateTimeInterface;
use plugin\admin\app\model\Base;

/**
 * @property integer $id 预约id(主键)
 * @property integer $user_id 用户id
 * @property integer $game_id 游戏id
 * @property integer type 类型,0=自动预约,1=真实预约
 * @property integer $create_time 创建时间
 * @property integer $update_time 更新时间
 */
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
