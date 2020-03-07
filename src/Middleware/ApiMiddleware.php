<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use FastRoute\Dispatcher;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Lengbin\Auth\AbstractAuth;
use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Hyperf\Auth\TraitAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AuthMiddleware
 * thanks yii
 * @package Common\middleware\auth
 */
class ApiMiddleware extends AbstractAuth implements MiddlewareInterface
{
    use TraitAuth;

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return ArrayHelper::getValue($this->getAuthConfig(), 'api', []);
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
     * @throws \Lengbin\Auth\Exception\InvalidTokenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $dispatched = $request->getAttribute(Dispatched::class);
        if ($dispatched->status !== Dispatcher::FOUND) {
            return $handler->handle($request);
        }
        [$isPublic, $isWhitelist] = $this->checkRouter($request, $dispatched);
        $user = $this->getUser($request, $isPublic, $isWhitelist);
        $request = Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($user) {
            return $request->withAttribute($this->requestName, $user);
        });
        return $handler->handle($request);
    }
}
