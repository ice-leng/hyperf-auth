<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\User\Event;

use Lengbin\Hyperf\Auth\IdentityInterface;

class AfterLoginEvent
{
    private $identity;
    private $duration;

    public function __construct(IdentityInterface $identity, int $duration)
    {
        $this->identity = $identity;
        $this->duration = $duration;
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
