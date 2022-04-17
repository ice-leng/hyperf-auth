<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Mode;

use Lengbin\Hyperf\Auth\JwtHelper;
use Lengbin\Hyperf\Auth\JwtSubject;
use Lengbin\Hyperf\Auth\LoginInterface;

class JwtMode implements LoginInterface
{

    protected JwtHelper $jwtHelper;

    public function __construct(JwtHelper $jwtHelper)
    {
        $this->jwtHelper = $jwtHelper;
    }

    public function makeToke(string $sub, ?string $iss = null, array $data = []): string
    {
        $data['sub'] = $sub;
        if ($iss) {
            $data['iss'] = $iss;
        }
        return $this->jwtHelper->make($data);
    }

    public function logout(string $token): bool
    {
        return $this->jwtHelper->logout($token);
    }

    public function refreshToken(string $token): string
    {
        return $this->jwtHelper->refreshToken($token);
    }

    public function verifyToken(?string $token, bool $ignoreExpired = false): JwtSubject
    {
        return $this->jwtHelper->verifyToken($token, $ignoreExpired);
    }

    public function getTtl(): int
    {
        return $this->jwtHelper->getTtl();
    }
}