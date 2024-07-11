<?php

namespace plugin\admin\app\model;

use plugin\admin\app\model\Base;

/**
 * @property integer $id 主键id(主键)
 * @property string $name 邮箱/手机
 * @property integer $type 类型,1=手机,2=邮箱
 * @property integer $create_time 预约时间
 * @property integer $update_time 更新时间
 */
class GrGameReser extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gr_game_reser';

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
//    public $timestamps = false;


}
