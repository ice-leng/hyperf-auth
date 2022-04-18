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
        'log' => [
            'enable' => true,
            'group'  => 'default',
        ],
        // 是否单点登录
        'oss' => false,
        // 测试 key
        'x-test-flag' => 1
    ];    
```


Publish
-------
```php
      
php ./bin/hyperf.php vendor:publish lengbin/hyperf-auth

```

DemoMiddleware 
-----

```php
<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use Lengbin\Hyperf\Auth\JwtSubject;
use Lengbin\Hyperf\Auth\LoginFactory;use Lengbin\Hyperf\Auth\Mode\LoginInterface;use Psr\Http\Message\ServerRequestInterface;

class DemoMiddleware extends BaseAuthMiddleware
{

    // 测试 载体
    protected function getTestPayload(ServerRequestInterface $request)
    {
        return [
            // 设置 测试 载体数据
            //'userId' => $request->getParsedBody()['userId']
        ];
    }

    protected function handlePayload(ServerRequestInterface $request, JwtSubject $payload): array
    {
        // $data = $payload->data;

        // 验证 载体 数据，  载体类型
//        if (empty($data['userId'])) {
//            throw new \Exception();
//        }

        // 数据 相关  验证 查询
        // [key => value]
        return [];
    }
    
    // jwt 发布者
    protected function getIss(): string
    {
        return "demo";
    }
    
    protected function getLoginMode(): LoginInterface
    {
        return $this->container->get(LoginFactory::class)->get();
    }
    
    
//    /**
//     * 获取Token，  可以 复写 自定义 获取key
//     */
//    public function getToken(ServerRequestInterface $request): ?string
//    {
//        $token = $this->getTokenByRequest($request);
//        [$token] = sscanf($token, 'Bearer %s');
//        return $token;
//    }
//
//    /**
//     * 解析 jwt 数据， 可以 复写 自己验证 token
//     */
//    public function validateToken(?string $token): JwtSubject
//    {
//        return $this->loginFactory->verifyToken($token);
//    }
}

```

Using
------
```php

<?php
declare(strict_types=1);

namespace App\Controller\Client\V1;


/**
 * @ApiController(prefix="/api/v1/client", tag="客户端.登录", description="客户端.登录")
 */
class LoginController extends BaseController
{

    /**
     * @Inject
     * @var LoginFactory
     */
    protected LoginFactory $loginFactory;

    public function login(): ResponseInterface
    {
        $result = $this->loginFactory->get()->make(["user_id" => 1], LoginFactory::LOGIN_TYPE_CLIENT);
        return $this->response->success([
            'token' => $result,
        ]);
    }

    /**
     * @PostApi(path="/refreshToken", summary="刷新token", description="刷新token")
     */
    public function refreshToken(): ResponseInterface
    {
        $token = $this->request->getAttribute('token');
        $result = $this->loginFactory->get()->refreshToken($token);
        return $this->response->success([
            'token' => $result,
        ]);
    }

    /**
     * @PostApi(path="/logout", summary="注销", description="退出登录")
     * @Middleware(ClientMiddleware::class)
     *
     * @ApiResponse(code="0", template="success")
     */
    public function logout(): ResponseInterface
    {
        $token = $this->request->getAttribute('token');
        $this->loginFactory->get()->logout($token);
        return $this->response->success();
    }
}


```

案例中的jwt请看[详情](https://github.com/hyperf-ext/jwt)

支持jwt模式(刷新token)和token模式(自动刷新token)