<?php

namespace Caramia\AdminBundle\User\Manager;

use Caramia\AdminBundle\Entity\UserInterface;

interface UserManagerInterface
{
    /**
     * @param UserInterface $user
     *
     * @return void
     */
    public function createUser(UserInterface $user);
}
