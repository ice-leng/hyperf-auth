<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth;

use HyperfExt\Jwt\Contracts\JwtFactoryInterface;
use HyperfExt\Jwt\Exceptions\TokenExpiredException;
use HyperfExt\Jwt\Jwt;
use HyperfExt\Jwt\Token;
use Lengbin\Hyperf\Auth\Exception\InvalidTokenException;
use Throwable;

class JwtHelper
{
    /**
     * 提供了从请求解析 JWT 及对 JWT 进行一系列相关操作的能力。
     *
     * @var Jwt
     */
    protected Jwt $jwt;

    public function __construct(JwtFactoryInterface $jwtFactory)
    {
        $this->jwt = $jwtFactory->make();
    }

    public function make(array $data = []): string
    {
        $this->jwt->setCustomClaims([]);
        $subject = new JwtSubject();
        $subject->data = $data;
        return $this->jwt->fromSubject($subject);
    }

    protected function handleToken($token): Token
    {
        if (!$token instanceof Token) {
            $token = new Token($token);
        }
        return $token;
    }

    public function logout(string $token, bool $forceForever = false): bool
    {
        return !$this->jwt->setToken($this->handleToken($token))->invalidate($forceForever)->check();
    }

    public function refreshToken(string $token, bool $forceForever = false): string
    {
        try {
            $this->handleClaims($token, true);
            return $this->jwt->refresh($forceForever);
        } catch (Throwable $exception) {
            throw new InvalidTokenException();
        }
    }

    protected function handleClaims(string $token, bool $ignoreExpired = false): array
    {
        $data = $this->jwt->setToken($this->handleToken($token))->getPayload($ignoreExpired)->toArray();
        $defaultClaims = $this->jwt->getManager()->getPayloadFactory()->getDefaultClaims();
        foreach ($defaultClaims as $claim) {
            if ($claim === 'iss') {
                continue;
            }
            unset($data[$claim]);
        }
        $this->jwt->setCustomClaims($data);
        return $data;
    }

    public function verifyToken(?string $token, bool $ignoreExpired = false): JwtSubject
    {
        $payload = new JwtSubject();
        if (empty($token)) {
            $payload->invalid = true;
            return $payload;
        }
        try {
            $payload->data = $this->handleClaims($token, $ignoreExpired);
        } catch (Throwable $e) {
            if ($e instanceof TokenExpiredException && $e->getMessage() === 'Token has expired') {
                $payload->expired = true;
            }
        }
        return $payload;
    }

    public function getTtl(): int
    {
        return $this->jwt->getPayloadFactory()->getTtl();
    }
}