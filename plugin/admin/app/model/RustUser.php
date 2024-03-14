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

    public function getAvatarAttribute($value)
    {
        $uploadUrl = config('env.url.upload_url');
        if (empty($value)) {
            $value = $uploadUrl . config('env.url.avatar_url');
        } else {
            if (substr($value, 0, 4) == 'http' || substr($value, 0, 5) == 'https') {
                return $value;
            } else {
                $value = $uploadUrl . $value;
            }
        }
        return $value;
    }
}
