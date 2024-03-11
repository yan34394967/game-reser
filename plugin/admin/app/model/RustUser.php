<?php

namespace plugin\admin\app\model;

class RustUser extends Base
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
    protected $table = 'cc_user';

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


}
