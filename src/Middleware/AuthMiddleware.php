<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use FastRoute\Dispatcher;
use http\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Lengbin\Hyperf\Auth\AuthAnnotation;
use Lengbin\Hyperf\Auth\IdentityRepositoryInterface;
use Lengbin\Hyperf\Auth\Method\CompositeAuth;
use Lengbin\Hyperf\Auth\AuthInterface;
use Lengbin\Hyperf\Auth\Exception\AuthException;
use Lengbin\Hyperf\Auth\User\GuestIdentity;
use Lengbin\Hyperf\Auth\User\User;
use Lengbin\Hyperf\Helper\Arrays\ArrayHelper;
use Lengbin\Hyperf\Helper\StringHelper;
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
class AuthMiddleware implements MiddlewareInterface
{

    private const REQUEST_NAME = 'auth';

    private $requestName = self::REQUEST_NAME;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    protected function checkConfig()
    {
        $auth = $this->config->get('auth');

        $this->setRequestName(ArrayHelper::getValue($auth, 'requestName', self::REQUEST_NAME));

        if ($auth === null) {
            throw new InvalidArgumentException('Please set auth config');
        }

        if (empty($auth['identityClass'])) {
            throw new InvalidArgumentException('Please set auth config identityClass params');
        }

        if (empty($auth['method'])) {
            throw new InvalidArgumentException('Please set auth config method params');
        } else {
            if (!is_string($auth['method']) && !is_array($auth['method'])) {
                throw new InvalidArgumentException('Method params support string and array');
            }
        }

        return $auth;
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
     * @throws AuthException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched->status !== Dispatcher::FOUND) {
            return $handler->handle($request);
        }

        $auth = $this->checkConfig();

        $identityClass = make($auth['identityClass']);

        if (!$identityClass instanceof \Lengbin\Hyperf\Auth\IdentityRepositoryInterface) {
            throw new \RuntimeException($auth['identityClass'] . ' must implement ' . IdentityRepositoryInterface::class);
        }

        if (is_array($auth['method'])) {
            $authenticator = make(CompositeAuth::class, [$this->container]);
            $authenticator->setAuthMethods($auth['method']);
        } else {
            $authenticator = make($auth['method'], [$identityClass]);
            if (!$authenticator instanceof AuthInterface) {
                throw new \RuntimeException(get_class($authenticator) . ' must implement ' . AuthInterface::class);
            }
        }

        [$isPublic, $isWhitelist] = $this->checkRouter($dispatched);

        //不验证
        if ($isPublic === null) {
            $publicList = ArrayHelper::getValue($auth, 'public', []);
            $isPublic = $this->checkPath($request, $publicList);
        }

        //白名单
        if ($isWhitelist === null) {
            $whitelist = ArrayHelper::getValue($auth, 'whitelist', []);
            $isWhitelist = $this->checkPath($request, $whitelist);
        }

        $identity = $isPublic ? null : $authenticator->authenticate($request);

        $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($identityClass, $identity) {
            $eventDispatcher = $this->container->get(EventDispatcherInterface::class);

            $user = make(User::class, [$identityClass, $eventDispatcher]);
            if ($identity === null) {
                $identity = make(GuestIdentity::class);
            }
            $user->login($identity);
            return $request->withAttribute($this->requestName, $user);
        });

        if (!$isPublic && $identity === null && !$isWhitelist) {
            throw new AuthException();
        }

        return $handler->handle($request);
    }

    /**
     * request name
     *
     * @param string $name
     */
    public function setRequestName(string $name): void
    {
        $this->requestName = $name;
    }

    /**
     * check url path
     *
     * @param ServerRequestInterface $request
     * @param array                  $patterns
     *
     * @return bool
     */
    protected function checkPath(ServerRequestInterface $request, array $patterns = []): bool
    {
        $status = false;
        $path = $request->getUri()->getPath();
        foreach ($patterns as $pattern) {
            if (StringHelper::matchWildcard($pattern, $path)) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    /**
     * 检测路由
     *
     * @param Dispatched $dispatched
     *
     * @return array
     */
    protected function checkRouter($dispatched): array
    {
        [$class, $method] = $dispatched->handler->callback;
        $classMethodAnnotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        if (empty($classMethodAnnotations) || !ArrayHelper::keyExists($classMethodAnnotations, AuthAnnotation::class)) {
            return [null, null];
        }
        /**
         * @var AuthAnnotation $authAnnotation
         */
        $authAnnotation = $classMethodAnnotations[AuthAnnotation::class];
        return [$authAnnotation->isPublic, $authAnnotation->isWhitelist];
    }

}
