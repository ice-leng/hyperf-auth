<?php
/**
 *
 * 单个验证
 * return [
    'api'  => [
        // 全局变量 名称
        'requestName'   => 'api',
        // 实现类，请实现接口 \Lengbin\Auth\IdentityRepositoryInterface::class
        'identityClass' => \App\Model\User::class,
        // 验证器方法，支持
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class //默认接收参数名称：X-Api-Token
        // query : \Lengbin\Auth\Method\QueryParamAuth::class //默认接收参数名称：access-token
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 单个
        // 'method' =>  \Lengbin\Auth\Method\QueryParamAuth::class,
        // 如果为 数组 则为 混合验证
        // key => val  接收参数名称 => 验证类
        'method' => [
            \Lengbin\Auth\Method\HttpHeaderAuth::class,
            'token' => \Lengbin\Auth\Method\QueryParamAuth::class,
        ],
        //路由白名单。列如 /test/{id}, 可以使用*来通配, /test/*
        'whitelist'     => [],
        //公共访问，不走验证。列如 /test/{id}, 可以使用*来通赔, /test/*
        'public'        => [],
    ],
 * ];
 */

return [
    'api'  => [
        // 全局变量 名称
        'requestName'   => 'api',
        // 实现类，请实现接口 \Lengbin\Auth\IdentityRepositoryInterface::class
        'identityClass' => \App\Model\User::class,
        // 验证器方法，支持
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class //默认接收参数名称：X-Api-Token
        // query : \Lengbin\Auth\Method\QueryParamAuth::class //默认接收参数名称：access-token
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 单个
        // 'method' =>  \Lengbin\Auth\Method\QueryParamAuth::class,
        // 如果为 数组 则为 混合验证
        // key => val  接收参数名称 => 验证类
        'method' => [
            \Lengbin\Auth\Method\HttpHeaderAuth::class,
            \Lengbin\Auth\Method\QueryParamAuth::class,
        ],
        //路由白名单。列如 /test/{id}, 可以使用*来通配, /test/*
        'whitelist'     => [],
        //公共访问，不走验证。列如 /test/{id}, 可以使用*来通配, /test/*
        'public'        => [],
    ],
    'web'  => [
        // 全局变量 名称
        'requestName' => 'web',
        'identityClass' => \App\Model\User::class,
        // 过期时间
        'timeout' => 8 * 60 * 60,
        // 跳转页面
        'redirect' => '/',
        'public'    => [],
    ],
    // todo 未实现，目前没有场景做，先放着
    'sign' => [
        'requestName' => 'auth',
        'identityClass' => \App\Model\User::class,
        'method'    => \Lengbin\Auth\Method\QueryParamAuth::class,
        'whitelist' => [],
        'public'    => [],
    ],
];
