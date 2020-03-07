<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Middleware;

use Lengbin\Auth\AbstractAuth;
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
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        // TODO: Implement getConfig() method.
    }
}
