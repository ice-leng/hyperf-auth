<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Mode;


use Lengbin\Hyperf\Auth\JwtHelper;
use Lengbin\Hyperf\Auth\JwtSubject;
use Lengbin\Hyperf\Auth\LoginInterface;

class TokenMode implements LoginInterface
{

    protected JwtHelper $jwtHelper;

    public function __construct(JwtHelper $jwtHelper)
    {
        $this->jwtHelper = $jwtHelper;
    }

    public function makeToke(string $sub, ?string $iss = null, array $data = []): string
    {
        //  TODO: Implement make() method.
    }

    public function logout(string $token): bool
    {
        // TODO: Implement logout() method.
    }

    public function refreshToken(string $token): string
    {
        // TODO: Implement refreshToken() method.
    }

    public function verifyToken(?string $token, bool $ignoreExpired = false): JwtSubject
    {
        // TODO: Implement verifyToken() method.
    }

    public function getTtl(): int
    {
        // TODO: Implement getTtl() method.
    }
}