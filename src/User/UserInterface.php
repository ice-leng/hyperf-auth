<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Auth\User;

use Lengbin\Hyperf\Auth\IdentityInterface;

interface UserInterface
{
    /**
     * login
     *
     * @param IdentityInterface $identity
     * @param int               $duration
     *
     * @return mixed
     */
    public function login(IdentityInterface $identity, $duration = 0);

    /**
     * logout
     *
     * @param bool $destroySession
     *
     * @return mixed
     */
    public function logout($destroySession = true);

    /**
     * is Guest
     *
     * @return bool
     */
    public function isGuest();

    /**
     * id
     * @return mixed
     */
    public function getId();

    /**
     * Identity
     *
     * @param bool $autoRenew
     *
     * @return mixed
     */
    public function getIdentity($autoRenew = true);

    /**
     * permission
     *
     * @param string $permissionName
     * @param array  $params
     *
     * @return mixed
     */
    public function can(string $permissionName, array $params = []);
}
