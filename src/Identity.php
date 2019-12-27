<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth;

class Identity implements IdentityRepositoryInterface, IdentityInterface
{

    /**
     * @inheritDoc
     */
    public function getId(): ?string
    {
        // TODO: Implement getId() method.
    }

    public function findIdentity(string $id): ?IdentityInterface
    {
        // TODO: Implement findIdentity() method.
    }

    /**
     * @inheritDoc
     */
    public function findIdentityByToken(string $token, string $type): ?IdentityInterface
    {
        // TODO: Implement findIdentityByToken() method.
    }
}
