<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Lengbin\Hyperf\Auth;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \Lengbin\Auth\Method\HttpHeaderAuth::class => \Lengbin\Auth\Method\HttpHeaderAuth::class,
                \Lengbin\Auth\Method\QueryParamAuth::class => \Lengbin\Auth\Method\QueryParamAuth::class,
                \Lengbin\Auth\Method\SignAuth::class       => \Lengbin\Auth\Method\SignAuth::class,
                \Lengbin\Auth\AuthSessionInterface::class  => \Lengbin\Hyperf\Auth\AuthSession::class,
            ],
            'publish'      => [
                [
                    'id'          => 'auth',
                    'description' => 'The config for auth.',
                    'source'      => __DIR__ . '/../publish/auth.php',
                    'destination' => BASE_PATH . '/config/autoload/auth.php',
                ],
            ],
        ];
    }
}
