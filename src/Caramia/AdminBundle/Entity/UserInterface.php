<?php

namespace Caramia\AdminBundle\Entity;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    public function encodePassword(PasswordEncoderInterface $encoder);
}
