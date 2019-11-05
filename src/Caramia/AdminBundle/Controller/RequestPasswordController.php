<?php

namespace Caramia\AdminBundle\Controller;

use Caramia\AdminBundle\User\Password\RequestPassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class RequestPasswordController extends Controller
{
    /**
     * @Route("/request-password", name="request_password")
     * @Method("GET|POST")
     */
    public function requestPasswordAction(Request $request)
    {
        $form = $this->createForm('request_password_form', new RequestPassword());

        $errors = null;


        if ($this->getRequestPasswordFormHandler()->handle($form, $request)) {
            $translator = $this->get('translator');
            $this->addFlash('success', $translator->trans('request_password.success', [], 'Admin'));

            return $this->redirect($this->generateUrl('security_login_form'));
        }
        else {
            $errors = $form->getErrors();
        }

        return $this->render('CaramiaAdminBundle:user:request-password.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    /**
     * @return \Caramia\AdminBundle\Form\Handler\FormHandlerInterface
     */
    protected function getRequestPasswordFormHandler()
    {
        return $this->container->get('caramia_admin.request_password.handler');
    }
}
