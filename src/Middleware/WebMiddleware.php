<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use FastRoute\Dispatcher;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Hyperf\Utils\Str;
use Lengbin\Auth\AbstractAuth;
use Lengbin\Auth\AuthSessionInterface;
use Lengbin\Auth\User\AccessCheckerInterface;
use Lengbin\Auth\User\User;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Hyperf\Auth\TraitAuth;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthMiddleware
 * thanks yii
 * @package Common\middleware\auth
 */
class WebMiddleware extends AbstractAuth implements MiddlewareInterface
{
    use TraitAuth;

    protected $authSession;

    public function __construct(ContainerInterface $container, EventDispatcherInterface $eventDispatcher, AuthSessionInterface $authSession)
    {
        parent::__construct($container, $eventDispatcher);
        $this->authSession = $authSession;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return ArrayHelper::getValue($this->getAuthConfig(), 'web', []);
    }

    protected function getRedirect($request)
    {
        $uri = $request->getUri();
        $url = ArrayHelper::getValue($this->getConfig(), 'redirect', '/');
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }
        return $uri->getScheme() . '://' . $uri->getAuthority() . (Str::startsWith($url, '/') ? $url : '/' . $url);
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched->status !== Dispatcher::FOUND) {
            return $handler->handle($request);
        }

        $identityClass = $this->getIdentityClass();
        $user = new User($identityClass, $this->eventDispatcher);
        $user->authTimeout = ArrayHelper::getValue($this->getConfig(), 'timeout');
        $user->setSession($this->authSession);

        // 权限，如果没有 使用 rbac 扩展的话， 需要判断 一下
        if ( $this->container->has(AccessCheckerInterface::class) ) {
            $user->setAccessChecker($this->container->get(AccessCheckerInterface::class));
        }

        $identity = $user->getIdentity();

        $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($user) {
            return $request->withAttribute($this->requestName, $user);
        });

        $response = $handler->handle($request);

        [$isPublic, $isWhitelist] = $this->checkRouter($request, $dispatched);
        if ($isPublic || $identity->getId()) {
            return $response;
        }

        return Context::override(ResponseInterface::class, function (ResponseInterface $response) use ($request) {
            return $response->withStatus(302)->withAddedHeader('Location', $this->getRedirect($request));
        });
    }

}
