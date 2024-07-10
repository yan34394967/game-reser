<?php

namespace app\validate\send;

use app\validate\Request;

class SendValidate extends Request
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'email' => 'require|email',
        'mobile' => 'require|mobile',
        'code' => 'require|length:6'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式错误',
        'mobile.require' => '手机号不能为空',
        'mobile.email' => '手机号格式错误',
        'code.require' => '验证码不能为空',
        'code.length' => '验证码错误'
    ];

    /**
     * 过滤验证字段
     * @var \string[][]
     */
    protected $scene = [
        'sendMail' => ['email'],
        'sendMobile' => ['mobile'],
        'reserMail' => ['email', 'code'],
        'reserMobile' => ['mobile', 'code'],
    ];
}
