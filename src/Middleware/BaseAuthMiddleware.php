<?php
/**
 * Created by PhpStorm.
 * Date:  2022/2/20
 * Time:  10:50 AM
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use Hyperf\Context\Context;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Logger\LoggerFactory;
use Lengbin\Hyperf\Auth\Annotation\RouterAuthAnnotation;
use Lengbin\Hyperf\Auth\Exception\InvalidTokenException;
use Lengbin\Hyperf\Auth\Exception\TokenExpireException;
use Lengbin\Hyperf\Auth\JwtSubject;
use Lengbin\Hyperf\Auth\LoginFactory;
use Lengbin\Hyperf\Auth\LoginInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class BaseAuthMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    // 登录模型
    protected string $loginMode = LoginFactory::LOGIN_MODE_API;

    protected LoginInterface $login;

    public function __construct()
    {
        $this->login = make(LoginFactory::class)->get($this->loginMode);
    }

    /**
     * 检测注解路由， 实现路由白名单
     */
    protected function checkRouter(ServerRequestInterface $request): array
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        $annotation = RouterAuthAnnotation::class;
        $callback = $dispatched->handler->callback;
        [$class, $method] = is_array($callback) ? $callback : ['', ''];
        $annotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        $authAnnotation = $annotations[$annotation] ?? AnnotationCollector::getClassAnnotation($class, $annotation);

        $isPublic = $isWhite = $ignoreExpired = false;
        if ($authAnnotation !== null) {
            $isPublic = $authAnnotation->isPublic;
            $isWhite = $authAnnotation->isWhite;
            $ignoreExpired = $authAnnotation->ignoreExpired;
        }
        return [$isPublic, $isWhite, $ignoreExpired];
    }

    /**
     * 记录日志
     */
    protected function logger($data, string $name = 'hyperf'): void
    {
        $enable = config('auth.log.enable', true);
        if ($enable) {
            if (is_array($data)) {
                $data = json_encode($data,
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
            $group = config('auth.log.group', 'default');
            $this->container->get(LoggerFactory::class)->get($name, $group)->info($data);
        }
    }

    public function getTokenByRequest(ServerRequestInterface $request, string $key = 'authorization'): string
    {
        $token = $request->getHeaderLine($key);
        if (empty($token)) {
            $token = $request->getQueryParams()[$key] ?? '';
        }
        if (empty($token)) {
            $token = $request->getCookieParams()[$key] ?? '';
        }
        if (empty($token)) {
            $token = $request->getParsedBody()[$key] ?? '';
        }
        return $token;
    }

    /**
     * 获取Token，  可以 复写 自定义 获取key
     */
    public function getToken(ServerRequestInterface $request): ?string
    {
        $token = $this->getTokenByRequest($request);
        [$token] = sscanf($token, 'Bearer %s');
        return $token;
    }

    protected function getLoginMode(): LoginInterface
    {
        return $this->login;
    }

    /**
     * 解析 jwt 数据， 可以 复写 自己验证 token
     */
    public function validateToken(?string $token, bool $ignoreExpired): JwtSubject
    {
        return $this->getLoginMode()->verifyToken($token, $ignoreExpired);
    }

    /**
     * 真实 ip
     */
    public function getClientIp(ServerRequestInterface $request, string $headerName = 'x-real-ip'): string
    {
        $client = $request->getServerParams();
        $xri = $request->getHeader($headerName);
        if (!empty($xri)) {
            $clientAddress = $xri[0];
        } else {
            $clientAddress = $client['remote_addr'];
        }
        $xff = $request->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {
                // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {
                // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) {
                    $clientAddress = $list[0];
                }
            }
        }
        return $clientAddress;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 记录请求日志
        $this->logger([
            'user-agent' => $request->getHeaderLine('user-agent'),
            'ip' => $this->getClientIp($request),
            'host' => $request->getUri()->getHost(),
            'url' => $request->getUri()->getPath(),
            'post' => $request->getParsedBody(),
            'get' => $request->getQueryParams(),
        ], 'request');

        [$isPublic, $isWhite, $ignoreExpired] = $this->checkRouter($request);

        // 无需鉴权 1，公开的， 2白名单的
        $token = $this->getToken($request);
        if ($isPublic || (empty($token) && $isWhite)) {
            return $handler->handle($request);
        }

        $isTest = $request->getHeaderLine('x-test-flag') == config('auth.x-test-flag');
        if ($isTest) {
            $payload = new JwtSubject();
            $payload->data = $this->getTestPayload($request);
        } else {
            $payload = $this->validateToken($token, $ignoreExpired);
        }

        // 记录 jwt解析 日志
        $this->logger(get_object_vars($payload), 'request-payload');

        if ($payload->invalid || (!$isTest && !empty(static::getIss()) && static::getIss() !== ($payload->data['iss'] ?? ''))) {
            throw new InvalidTokenException();
        }

        if ($payload->expired) {
            throw new TokenExpireException();
        }

        $results = $this->handlePayload($request, $payload);
        $results['token'] = $token;
        foreach ($results as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        Context::set(ServerRequestInterface::class, $request);
        $response = $handler->handle($request);
        $this->logger($response->getBody()->getContents(), 'response');
        return $response;
    }

    /**
     * 获取测试载体
     */
    abstract protected function getTestPayload(ServerRequestInterface $request): array;

    /**
     * 处理数据
     */
    abstract protected function handlePayload(ServerRequestInterface $request, JwtSubject $payload): array;


    /**
     * jwt签发者, 如果为空 则使用 当前登录uri
     */
    abstract public static function getIss(): ?string;
}
