<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Mode;

use Lengbin\Hyperf\Auth\JwtSubject;

interface LoginInterface
{

    public function makeToke(string $sub, ?string $iss = null, array $data = []): string;

    public function logout(string $token): bool;

    public function refreshToken(string $token): string;

    public function verifyToken(?string $token, bool $ignoreExpired = false): JwtSubject;

    public function getTtl(): int;
}