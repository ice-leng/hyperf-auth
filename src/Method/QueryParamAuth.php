<?php
declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\Method;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Lengbin\Hyperf\Auth\AuthInterface;
use Lengbin\Hyperf\Auth\IdentityInterface;
use Lengbin\Hyperf\Auth\IdentityRepositoryInterface;

/**
 * QueryParamAuth supports the authentication based on the access token passed through a query parameter.
 */
final class QueryParamAuth implements AuthInterface
{
    private const TOKEN_PARAM = 'access-token';
    /**
     * @var string the parameter name for passing the access token
     */
    private $tokenParam = self::TOKEN_PARAM;
    /**
     * @var IdentityRepositoryInterface
     */
    private $identityRepository;

    public function __construct(IdentityRepositoryInterface $identityRepository)
    {
        $this->identityRepository = $identityRepository;
    }

    public function authenticate(ServerRequestInterface $request): ?IdentityInterface
    {
        $accessToken = $request->getQueryParams()[$this->tokenParam] ?? null;
        if (is_string($accessToken)) {
            return $this->identityRepository->findIdentityByToken($accessToken, get_class($this));
        }

        return null;
    }

    public function challenge(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    public function setTokenParam(string $param): void
    {
        $this->tokenParam = $param;
    }
}
