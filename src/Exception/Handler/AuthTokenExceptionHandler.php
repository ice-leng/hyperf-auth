<?php

namespace Lengbin\Hyperf\Auth\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Lengbin\Auth\Exception\InvalidTokenException;
use Lengbin\Hyperf\Common\Error\CommentErrorCode;
use Lengbin\Hyperf\Common\Exception\Handler\ExceptionHandlerTrait;
use Lengbin\Jwt\Exception\ExpiredJwtException;
use Lengbin\Jwt\Exception\InvalidJwtException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AuthTokenExceptionHandler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();
        $this->formatLog($throwable);
        $error = $throwable instanceof ExpiredJwtException ? CommentErrorCode::TOKEN_EXPIRED() : CommentErrorCode::INVALID_TOKEN();

        return $this->response->fail($error->getValue(), $error->getMessage());
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof InvalidTokenException || $throwable instanceof InvalidJwtException || $throwable instanceof ExpiredJwtException;
    }
}
