<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class AuthAnnotation extends AbstractAnnotation
{
    // 是否为公共访问， 不走auth验证
    public $isPublic = false;

    // 是否为白名单， 走auth验证，如果不存在token不报错
    public $isWhitelist = false;
}
