<?php

namespace Caramia\AppBundle\User\Password;

use Caramia\AppBundle\Form\Handler\FormHandlerInterface;
use Caramia\AppBundle\User\Manager\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class ChangePasswordFormHandler implements FormHandlerInterface
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

        $this->handler->updateCredentials($form->getData()->getUser(), $form->getData()->getNewPassword());

        return true;
    }
}
