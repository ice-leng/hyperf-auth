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
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class
        // query : \Lengbin\Auth\Method\QueryParamAuth::class
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 如果为 数组 则为 混合验证
        'method' => [
            \Lengbin\Auth\Method\HttpHeaderAuth::class,
            \Lengbin\Auth\Method\QueryParamAuth::class,
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
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class
        // query : \Lengbin\Auth\Method\QueryParamAuth::class
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 如果为 数组 则为 混合验证
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
        'requestName' => 'auth',
        'identityClass' => \App\Model\User::class,
        // 验证器方法，支持
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class
        // query : \Lengbin\Auth\Method\QueryParamAuth::class
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 如果为 数组 则为 混合验证
//    'method'        => [
//        \Lengbin\Auth\Method\HttpHeaderAuth::class,
//        \Lengbin\Auth\Method\QueryParamAuth::class,
//    ],

        'method'    => \Lengbin\Auth\Method\QueryParamAuth::class,
        //路由白名单 此参数。 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
        'whitelist' => [],
        //公共访问，不走验证。此参数 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
        'public'    => [],
    ],
    'sign' => [
        // 全局变量 名称
        'requestName' => 'auth',
        'identityClass' => \App\Model\User::class,
        // 验证器方法，支持
        // header: \Lengbin\Auth\Method\HttpHeaderAuth::class
        // query : \Lengbin\Auth\Method\QueryParamAuth::class
        // sign  : \Lengbin\Auth\Method\SignAuth::class
        // 如果为 数组 则为 混合验证
//    'method'        => [
//        \Lengbin\Auth\Method\HttpHeaderAuth::class,
//        \Lengbin\Auth\Method\QueryParamAuth::class,
//    ],

        'method'    => \Lengbin\Auth\Method\QueryParamAuth::class,
        //路由白名单 此参数。 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
        'whitelist' => [],
        //公共访问，不走验证。此参数 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
        'public'    => [],
    ],
];
