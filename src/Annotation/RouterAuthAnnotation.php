<?php
declare (strict_types=1);

namespace Lengbin\Hyperf\Auth\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use Attribute;

/**
 * 路由权限注解
 * 定义 白名单
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class RouterAuthAnnotation extends AbstractAnnotation
{
    // 是否为公共访问， 不走auth验证
    public bool $isPublic = false;

    // 是否为白名单， 走auth验证，如果不存在token不报错
    public bool $isWhitelist = false;

    public function __construct(bool $isPublic = false, bool $isWhitelist = false)
    {
        $this->isPublic = $isPublic;
        $this->isWhitelist = $isWhitelist;
    }
}
