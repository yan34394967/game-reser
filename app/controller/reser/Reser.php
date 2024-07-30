<?php

namespace app\controller\reser;

use app\common\controller\ApiController;
use app\common\model\reser\User;
use app\common\service\reser\ReserService;
use app\validate\send\SendValidate;

class Reser extends ApiController
{
    /**
     * 预约游戏
     * @param SendValidate $sendValidate
     * @param ReserService $reserService
     * @return \think\response\Json
     * @throws \RedisException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reser(SendValidate $sendValidate, ReserService $reserService)
    {
        $name = request()->post('name', '');
        $code = request()->post('code', '');
        // 检测是手机还是邮箱
        if (! filter_var($name, FILTER_VALIDATE_EMAIL)) {
            $sence = 'reserMobile';
            $field = 'mobile';
            $type = 1;
        } else {
            $sence = 'reserMail';
            $field = 'email';
            $type = 2;
        }
        $sendValidate->goCheck($sence, [
            $field => $name,
            'code' => $code
        ]);
        $res = $reserService::gameReser($name, $code, $type);
        if (!$res) {
            return self::error('预约失败');
        }
        return self::success([], '预约成功');
    }

    /**
     * 预约记录
     * @param ReserService $reserService
     * @return \support\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reserLog(ReserService $reserService)
    {
        $page = request()->post('page', 1, 'intval');
        $limit = parent::paramNum();
        $res = $reserService::reserLog($page, $limit);
        if (!$res) {
            return self::success([]);
        }
        return self::success($res);
    }

    /**
     * 填充数据
     * @return \support\Response
     * @throws \Exception
     */
    public function yuyuezhe()
    {
        $where = [
            ['status', '=', 1],
            ['type', '=', 1],
        ];
        $users = User::where($where)
            ->order('create_time', 'desc')
            ->limit(5000)
            ->column('email');
        if ($users) {
            $addData = [];
            foreach ($users as $user) {
                if ($user) {
                    $addData[] = [
                        'name' => $user,
                        'type' => 2,
                        'status' => 0
                    ];
                }
            }
            (new \app\common\model\reser\GameReser())->saveAll($addData);
        }
        return self::success();
    }
}
