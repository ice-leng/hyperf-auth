<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth;

use Hyperf\Contract\SessionInterface;
use Lengbin\Auth\AuthSessionInterface;

class AuthSession implements AuthSessionInterface
{
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, $value): void
    {
        $this->session->set($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $name)
    {
        return $this->session->remove($name);
    }

    /**
     * @inheritDoc
     */
    public function destroy()
    {
        $this->session->invalidate();
    }
}
