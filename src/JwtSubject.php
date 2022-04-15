<?php
/**
 * Created by PhpStorm.
 * Date:  2021/10/22
 * Time:  6:51 下午
 */

declare(strict_types=1);
namespace Lengbin\Hyperf\Auth;

use HyperfExt\Jwt\Contracts\JwtSubjectInterface;

class JwtSubject implements JwtSubjectInterface
{
    /**
     * @var string
     */
    public string $key = '';

    /**
     * @var array
     */
    public array $data = [];

    /**
     * 是否过期
     * @var bool
     */
    public bool $expired = false;

    /**
     * 是否失效
     * @var bool
     */
    public bool $invalid = false;

    /**
     * token
     * @var string
     */
    public string $token = '';

    public function getJwtIdentifier()
    {
        return $this->key;
    }

    public function getJwtCustomClaims(): array
    {
        return $this->data;
    }
}
