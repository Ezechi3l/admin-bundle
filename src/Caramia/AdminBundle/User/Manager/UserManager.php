<?php

namespace Caramia\AdminBundle\User\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Caramia\AdminBundle\Entity\UserInterface;
use Caramia\AdminBundle\User\Manager\UserManagerInterface;
use Caramia\AdminBundle\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Caramia\AdminBundle\Event\UserDataEvent;

class UserManager implements UserManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param ObjectManager                 $manager
     * @param EncoderFactoryInterface       $encoderFactory
     * @param EventDispatcherInterface      $dispatcher
     * @param UserPasswordEncoderInterface  $encoder
     * @param UserRepository                $userRepository
     */
    public function __construct(
        ObjectManager $manager,
        EncoderFactoryInterface $encoderFactory,
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository
    ) {
        $this->objectManager = $manager;
        $this->encoderFactory = $encoderFactory;
        $this->dispatcher = $dispatcher;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function createUser(UserInterface $user)
    {
        $user->encodePassword($this->encoderFactory->getEncoder($user));
        $this->persistAndFlushUser($user);
    }

    /**
     * @param UserInterface $user
     */
    private function persistAndFlushUser(UserInterface $user)
    {
        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    public function updateCredentials(UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $user->encodePassword($this->encoderFactory->getEncoder($user));
        $this->objectManager->flush();
    }

    public function isPasswordValid(UserInterface $user, $plainPassword)
    {
        return $this->encoder->isPasswordValid($user, $plainPassword);
    }

    public function getUserByIdentifier($identifier)
    {
        return $this->userRepository->getUserByEmail($identifier);
    }

    public function sendRequestPassword($user)
    {
        $this->dispatcher->dispatch(
            'caramia_admin.new_password_requested', new UserDataEvent($user)
        );
    }

    public function updateConfirmationTokenUser(UserInterface $user, $token) {
        $user->setConfirmationToken($token);
        $user->setIsAlreadyRequested(true);
        $this->objectManager->flush();
    }

    public function getUserByConfirmationToken($token)
    {
        return $this->userRepository->findOneByConfirmationToken($token);
    }

    public function clearConfirmationTokenUser(UserInterface $user) {
        $user->setConfirmationToken(null);
        $user->setIsAlreadyRequested(false);
    }
}
