<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\User;

use Lengbin\Hyperf\Auth\IdentityInterface;

class GuestIdentity implements IdentityInterface
{
    public function getId(): ?string
    {
        return null;
    }
}
