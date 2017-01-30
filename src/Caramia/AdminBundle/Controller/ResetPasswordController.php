<?php

namespace Caramia\AdminBundle\Controller;

use Caramia\AdminBundle\User\Password\ResetPassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class ResetPasswordController extends Controller
{

    /**
     * @Route("/reset-password", name="reset_password")
     * @Method("GET|POST")
     */
    public function requestPasswordAction(Request $request)
    {
        try {
            $form = $this->createForm('reset_password_form', new ResetPassword());

            if ($this->getResetPasswordFormHandler()->handle($form, $request)) {
                $translator = $this->get('translator');
                $this->addFlash('success', $translator->trans('reset_password.success', [], 'Admin'));
                return $this->redirect($this->generateUrl('security_login_form'));
            }

            return $this->render('CaramiaAdminBundle:user:reset-password.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirect($this->generateUrl('security_login_form'));
        }
    }

    /**
     * @return \Caramia\AdminBundle\Form\Handler\FormHandlerInterface
     */
    protected function getResetPasswordFormHandler()
    {
        return $this->container->get('caramia_admin.reset_password.handler');
    }
}
