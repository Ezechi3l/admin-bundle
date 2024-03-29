<?php

namespace Caramia\AdminBundle\User\Password;

use Caramia\AdminBundle\Form\Handler\FormHandlerInterface;
use Caramia\AdminBundle\User\Manager\UserManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestPasswordFormHandler implements FormHandlerInterface
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

        $this->handler->sendRequestPassword($form->getData()->getUser());

        return true;
    }
}
