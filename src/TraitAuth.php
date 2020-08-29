<?php

namespace Lengbin\Hyperf\Auth;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Lengbin\Hyperf\Common\Helper\AnnotationHelper;
use Psr\Http\Message\ServerRequestInterface;

trait TraitAuth
{

    /**
     * 配置
     * @return array|null
     */
    public function getAuthConfig(): ?array
    {
        return $this->container->get(ConfigInterface::class)->get('auth');
    }

    /**
     * 检测注解路由
     *
     * @param ServerRequestInterface $request
     * @param Dispatched             $dispatched
     *
     * @return array
     */
    public function checkRouter(ServerRequestInterface $request, Dispatched $dispatched): array
    {
        // 优先级 ， 方法 大于 类
        /**
         * @var RouterAuthAnnotation $authAnnotation
         */
        $authAnnotation = AnnotationHelper::getClassMethodAnnotation(RouterAuthAnnotation::class, $dispatched);
        if (is_null($authAnnotation)) {
            $authAnnotation = AnnotationHelper::getClassAnnotation(RouterAuthAnnotation::class, $dispatched);
        }
        if ($authAnnotation !== null) {
            $isPublic = $authAnnotation->isPublic;
            $isWhitelist = $authAnnotation->isWhitelist;
        } else {
            $path = $request->getUri()->getPath();
            $isPublic = $this->checkPublicList($path);
            $isWhitelist = $this->checkWhitelist($path);
        }
        return [$isPublic, $isWhitelist];
    }
}
