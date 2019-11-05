<?php

namespace Caramia\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caramia\AdminBundle\User\Manager\UserManagerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Caramia\AdminBundle\User\Password\ResetPassword;

class ResetPasswordType extends AbstractType
{
    /**
     *
     * @var UserManagerInterface $handler
     */
    private $handler;

    /**
     *
     * @var Request $request
     */
    private $request;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager, RequestStack $requestStack)
    {
        $this->handler = $userManager;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, array(
            'first_name'  => 'password',
            'second_name' => 'confirm',
            'type'        => 'password',
            'options' => array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ),
            'first_options'  => array('label' => 'reset_password.password'),
            'second_options' => array('label' => 'reset_password.confirm'),
        ));

        $builder->add('Reset Password', SubmitType::class, array(
            'attr' => array(
                'class' => 'btn btn-primary',
                'required' => 'required',
            ),
            'label' => 'reset_password.submit'
        ));

        $builder->addEventListener(
        FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!$data instanceof ResetPassword) {
                    throw new \RuntimeException('ChangePassword instance required.');
                }
                $token = $this->request->query->get('token');

                if (!$token) {
                   throw new \Exception('Incorrect Token.');
                }

                $user = $this->handler->getUserByConfirmationToken($token);

                if (!$user) {
                   throw new \Exception('User not identified in our database with this token.');
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Caramia\AdminBundle\User\Password\ResetPassword',
            'translation_domain' => 'Admin',
        ]);
    }

    public function getName()
    {
        return 'reset_password_form';
    }
}
