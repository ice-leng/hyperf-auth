<?php
/**
 *
 * 单个验证
 * return [
 *      // 全局变量 名称
 *      'requestName'   => 'auth',
 *       // 验证器方法，支持
 *       // header: \Lengbin\Hyperf\Auth\Method\HttpHeaderAuth::class
 *       // query : \Lengbin\Hyperf\Auth\Method\QueryParamAuth::class
 *       // sign  : \Lengbin\Hyperf\Auth\Method\SignAuth::class
 *       // 如果为 数组 则为 混合验证
 *      'method' => ''
 *      //路由白名单。此参数 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
 *      'whitelist'     => [],
 *      //公共访问，不走验证。此参数 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
 *      'public'     => [],
 * ];
 */
return [
    // 全局变量 名称
    'requestName'   => 'auth',
    // 验证器方法，支持
    // header: \Lengbin\Hyperf\Auth\Method\HttpHeaderAuth::class
    // query : \Lengbin\Hyperf\Auth\Method\QueryParamAuth::class
    // sign  : \Lengbin\Hyperf\Auth\Method\SignAuth::class
    // 如果为 数组 则为 混合验证
    'method'        => [
        \Lengbin\Hyperf\Auth\Method\HttpHeaderAuth::class,
        \Lengbin\Hyperf\Auth\Method\QueryParamAuth::class,
    ],
    //路由白名单 此参数。 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
    'whitelist'     => [],
    //公共访问，不走验证。此参数 不适合带path参数路由，比如有 /test/{id}, 如果想匹配请使用注解 AuthAnnotation
    'public'        => [],
];
