<?php

namespace Caramia\AdminBundle\Controller;

use Caramia\AdminBundle\User\Password\ChangePassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/account")
 */
class ChangePasswordController extends Controller
{
    /**
     * @Route("/change-password", name="change_password")
     * @Method("GET|POST")
     */
    public function changePasswordAction(Request $request)
    {
        $data = new ChangePassword($this->getUser());
        $form = $this->createForm('change_password', $data);

        if ($this->getChangePasswordFormHandler()->handle($form, $request)) {
            $this->addFlash('success', 'Password has been changed successfully.');
            return $this->redirect($this->generateUrl('user_dashboard'));
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \AppBundle\Form\Handler\FormHandlerInterface
     */
    protected function getChangePasswordFormHandler()
    {
        return $this->container->get('app.change_password.handler');
    }


}
