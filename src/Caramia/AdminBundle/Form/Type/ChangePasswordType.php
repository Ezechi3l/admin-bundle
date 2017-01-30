<?php

namespace Caramia\AdminBundle\Form\Type;

use Caramia\AdminBundle\User\Password\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', 'password', ['required' => false ])
            ->add('newPassword', 'repeated', [
                'required' => false,
                'type' => 'password',
                'first_options' => [
                    'label' => 'New Password',
                ],
                'second_options' => [
                    'label' => 'Confirmation',
                ]
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!$data instanceof ChangePassword) {
                    throw new \RuntimeException('ChangePassword instance required.');
                }

                $oldPassword = $data->getOldPassword();
                $newPassword = $data->getNewPassword();
                $form = $event->getForm();
                if (!$oldPassword || !$newPassword || count($form->getErrors(true))) {
                    return;
                }

                $user = $data->getUser();

                if (!$this->handler->isPasswordValid($user, $oldPassword)) {
                    $form = $event->getForm();
                    $form->get('oldPassword')->addError(new FormError('Previous password is not valid.'));
                    return;
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Caramia\AdminBundle\User\Password\ChangePassword',
        ]);
    }


    public function getName()
    {
        return 'change_password';
    }
}
