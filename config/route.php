<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

/**
 * api接口
 */
Route::group('/api', function () {
    // 邮箱发送验证码
    Route::add(['POST','OPTIONS'], '/email/send', [app\controller\sms\Send::class, 'sendMail']);
    // 手机发送验证码
    Route::add(['POST','OPTIONS'], '/mobile/send', [app\controller\sms\Send::class, 'sendMobile']);
    // 预约
    Route::add(['POST','OPTIONS'], '/game/reser', [app\controller\reser\Reser::class, 'reser']);
    // 测试填充数据
    Route::add(['POST','OPTIONS'], '/add/reser', [app\controller\reser\Reser::class, 'yuyuezhe']);

    /*
     * 验证用户登录
     */
    Route::group('/', function () {
//        // 获取用户信息
//        Route::add(['POST', 'OPTIONS'], 'user/info', [app\controller\user\UserController::class, 'getUser']);
//        // 用户退出
//        Route::add(['POST', 'OPTIONS'], 'sign/out', [app\controller\user\UserController::class, 'signOut']);


    })->middleware([
        App\middleware\AuthCheck::class,
    ]);

    /*
     * 验证登录并防重复提交操作
     */
    Route::group('/', function () {

    })->middleware([
        App\middleware\AuthCheck::class,
        App\middleware\AuthRequest::class,
    ]);
});

// 关闭自动路由
Route::disableDefaultRoute();




