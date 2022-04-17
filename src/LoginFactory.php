<?php
/**
 * Created by PhpStorm.
 * Date:  2021/10/22
 * Time:  4:03 下午
 */

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth;

use Lengbin\Hyperf\Auth\Mode\JwtMode;
use Lengbin\Hyperf\Auth\Mode\TokenMode;

class LoginFactory
{
    public const LOGIN_MODE_API = 'api';
    public const LOGIN_MODE_SESSION = 'session';

    public const MAP = [
        self::LOGIN_MODE_API => JwtMode::class,
        self::LOGIN_MODE_SESSION => TokenMode::class,
    ];

    public function get(string $mode = self::LOGIN_MODE_API): LoginInterface
    {
        return make(self::MAP[$mode]);
    }
}
