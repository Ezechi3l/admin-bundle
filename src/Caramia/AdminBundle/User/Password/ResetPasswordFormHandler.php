<?php

namespace Caramia\AdminBundle\User\Password;

use Caramia\AdminBundle\Form\Handler\FormHandlerInterface;
use Caramia\AdminBundle\User\Manager\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordFormHandler implements FormHandlerInterface
{
    /**
     *
     * @var UserManagerInterface
     */
    private $handler;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->handler = $userManager;
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param array         $options
     *
     * @return bool
     */
    public function handle(FormInterface $form, Request $request, array $options = null)
    {
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return false;
        }

        $token = $request->query->get('token');
        $user = $this->handler->getUserByConfirmationToken($token);

        $this->handler->clearConfirmationTokenUser($user);
        $this->handler->updateCredentials($user, $form->getData()->getPassword());

        return true;
    }
}
