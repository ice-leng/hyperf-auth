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

Configs
-----
``` php
    // 配置 /config/autoload/auth.php
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
        
    // 依赖注入 /config/autoload/dependencies.php
    return [
        // User implements IdentityRepositoryInterface, IdentityInterface
        IdentityRepositoryInterface::class => \App\Model\User::class,
    ];
    
    //重新定义 无效token 异常 请捕获 Lengbin\Hyperf\Auth\Exception\InvalidTokenException
    //  /config/autoload/exceptions.php
    return [
        'handler' => [
            'http' => [
                \Common\Exception\Handler\InvalidTokenExceptionHandler::class,
            ],
        ],
    ];

    // 中间件
    // /config/autoload/middlewares.php
    return [
        'http' => [
            \Lengbin\Hyperf\Auth\Middleware\CorsMiddleware::class,
            \Lengbin\Hyperf\Auth\Middleware\AuthMiddleware::class,
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
        $requestName = $config->get('auth.requestName', 'auth');
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

案例中的token请看[详情](https://github.com/ice-leng/hyperf-jwt)
