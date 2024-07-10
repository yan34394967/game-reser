<?php

namespace app\common\model;

use think\Model;

class BaseModel extends Model
{
    // 自动更新时间
    protected $autoWriteTimestamp = true;
    protected $dateFormat = false; // 不转换时间格式

    /**
     * 查询单条数据
     * @param $where
     * @param $field
     * @param $order
     * @return array|false|mixed|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getFindData($where, $field = true, $order = ['id' => 'desc'])
    {
        if (empty($where) && ! is_array($where)) {
            return false;
        }
        $res = self::field($field)->where($where);
        if ($order) {
            $res->order($order);
        }
        return $res->find();
    }

    /**
     * 查询多条数据
     * @param $where
     * @param $field
     * @param $page
     * @param $num
     * @param $order
     * @return BaseModel[]|array|\think\Collection|\think\db\Query[]|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getDataByWhere($where, $field = true, $page = 0, $num = 10, $order = ['id' => 'desc'])
    {
        $res = self::field($field);
        if (! empty($where) && is_array($where)) {
            $res->where($where);
        }
        if ($page > 0) {
            $res->page($page, $num);
        }
        return $res->order($order)->select();
    }
}
