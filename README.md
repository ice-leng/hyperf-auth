<p align="center">
    <a href="https://hyperf.io/" target="_blank">
        <img src="https://hyperf.oss-cn-hangzhou.aliyuncs.com/hyperf.png" height="100px">
    </a>
    <h1 align="center">Hyperf Auth</h1>
    <br>
</p>

If You Like This Please Give Me Star

Install
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require lengbin/hyperf-auth
```

or add

```
"lengbin/hyperf-auth": "*"
```
to the require section of your `composer.json` file.

如果没有看懂可以参考[hyper-advanced](https://github.com/ice-leng/hyperf-advanced)

Configs
-----
``` php
    // 配置 /config/autoload/auth.php
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
    ];
            
    //重新定义 无效token 异常 请捕获 Lengbin\Hyperf\Auth\Exception\InvalidTokenException
    //  /config/autoload/exceptions.php
    return [
        'handler' => [
            'http' => [
                \Lengbin\Hyperf\Helper\Exception\Handler\InvalidTokenExceptionHandler::class,
            ],
        ],
    ];

    // 中间件
    // /config/autoload/middlewares.php
    return [
        'http' => [
             \Lengbin\Hyperf\Auth\Middleware\CorsMiddleware::class,
             \Lengbin\Hyperf\Auth\Middleware\ApiMiddleware::class,
        ],
    ];
    
```


Publish
-------
```php
      
php ./bin/hyperf.php vendor:publish lengbin/hyperf-auth

```

Usage
-----
```php

<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Lengbin\Hyperf\Auth\AuthAnnotation;
use Lengbin\Hyperf\Auth\User\UserInterface;
use Lengbin\Jwt\TokenInterface;

/**
 * Class IndexController
 * @package App\Controller
 * @Controller()
 */
class IndexController extends AbstractController
{

    /**
     * @Inject()
     * @var TokenInterface
     */
    protected $jwt;

    /**
     * 直接访问 不做验证 
     * @RequestMapping(path="/", methods={"get", "post"})
     * @AuthAnnotation(isPublic=true)  
     * @return array
     */
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();
        return [
            'method'  => $method,
            'message' => "Hello {$user}.",
            'token' => $this->jwt->makeToken(["username"=> 'ice', 'face'=> ''], 12),
        ];
    }

    /**
     * 路由白名单， 直接方法 或者 带token 访问
     * @GetMapping(path="/test/{id:\d{1,3}}")
     * @AuthAnnotation(isWhitelist=true)
     */
    public function test($id)
    {
        return ['11' =>  $id];
    }
    
    /**
     * auth
     * @return UserInterface
     */
    public function getAuth(): UserInterface
    {
        $config = $this->container->get(ConfigInterface::class);
        $requestName = $config->get('auth.api.requestName', 'api');
        return $this->request->getAttribute($requestName);
    }

    /** 
     * token验证访问
     * @GetMapping(path="/test2")
     */
    public function test2()
    {
        return [
            'id' => $this->getAuth()->getId()
        ];
    }

}
```

案例中的jwt请看[详情](https://github.com/ice-leng/hyperf-jwt)
