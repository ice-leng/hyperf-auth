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
        /**
         * @var AuthAnnotation $authAnnotation
         */
        $authAnnotation = AnnotationHelper::get(AuthAnnotation::class, $dispatched);
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
