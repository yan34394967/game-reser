<?php

namespace plugin\admin\app\controller;

use support\Request;
use support\Response;
use plugin\admin\app\model\GrGameReser;
use plugin\admin\app\controller\Crud;
use support\exception\BusinessException;

/**
 * 游戏预约 
 */
class GrGameReserController extends Crud
{
    
    /**
     * @var GrGameReser
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new GrGameReser;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('gr-game-reser/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return view('gr-game-reser/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::update($request);
        }
        return view('gr-game-reser/update');
    }

}
