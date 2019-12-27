<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\User\Event;

use Lengbin\Hyperf\Auth\IdentityInterface;

class BeforeLoginEvent
{
    private $identity;
    private $duration;
    private $isValid = true;

    public function __construct(IdentityInterface $identity, int $duration)
    {
        $this->identity = $identity;
        $this->duration = $duration;
    }

    public function invalidate(): void
    {
        $this->isValid = false;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getIdentity(): IdentityInterface
    {
        return $this->identity;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
