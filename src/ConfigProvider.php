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

use Lengbin\Hyperf\Auth\Method\HttpHeaderAuth;
use Lengbin\Hyperf\Auth\Method\QueryParamAuth;
use Lengbin\Hyperf\Auth\Method\SignAuth;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                IdentityRepositoryInterface::class => Identity::class,
                HttpHeaderAuth::class              => HttpHeaderAuth::class,
                QueryParamAuth::class              => QueryParamAuth::class,
                SignAuth::class                    => SignAuth::class,
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
