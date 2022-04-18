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
        $token = $this->jwtHelper->make($data);
        $this->handleOss($token, 'add');
        return $token;
    }

    protected function handleOss(string $token, string $event): bool
    {
        $status = false;
        if (!config('auth.oss', true)) {
            return $status;
        }
        $result = $this->jwtHelper->getClaims($token);
        switch ($event) {
            case "add":
                $this->jwtHelper->getStorage()->add($result['sub'], $result['jti'], $this->jwtHelper->getTtl());
                break;
            case "remove":
                $this->jwtHelper->getStorage()->destroy($result['sub']);
                break;
            case "check":
                $jti = $this->jwtHelper->getStorage()->get($result['sub']);
                $status = $jti !== $result['jti'];
                break;
        }
        return $status;
    }

    public function logout(string $token): bool
    {
        $this->handleOss($token, 'remove');
        return $this->jwtHelper->logout($token);
    }

    public function refreshToken(string $token): string
    {
        $newToken = $this->jwtHelper->refreshToken($token);
        $this->handleOss($newToken, 'add');
        return $newToken;
    }

    public function verifyToken(?string $token, bool $ignoreExpired = false): JwtSubject
    {
        $payload = $this->jwtHelper->verifyToken($token, $ignoreExpired);
        if (!$payload->expired && !$payload->invalid) {
            $payload->invalid = $this->handleOss($token, 'check');
        }
        return $payload;
    }

    public function getTtl(): int
    {
        return $this->jwtHelper->getTtl();
    }
}