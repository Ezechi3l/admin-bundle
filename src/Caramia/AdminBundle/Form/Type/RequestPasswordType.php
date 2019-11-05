<?php

namespace Caramia\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Translation\TranslatorInterface;
use Caramia\AdminBundle\User\Manager\UserManagerInterface;
use Caramia\AdminBundle\User\Password\RequestPassword;

class RequestPasswordType extends AbstractType
{
    /**
     *
     * @var UserManagerInterface
     */
    private $handler;
    /**
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param UserManagerInterface $userManager
     * @param TranslatorInterface $translator
     */
    public function __construct(UserManagerInterface $userManager, TranslatorInterface $translator)
    {
        $this->handler = $userManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'required' => 'required',
                ],
                'label' => 'request_password.email',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'request_password.submit',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!$data instanceof RequestPassword) {
                throw new \RuntimeException('RequestPassword instance required.');
            }

            $identifier = $data->getIdentifier();
            $form = $event->getForm();

            if (!$identifier || count($form->getErrors(true))) {
                return;
            }

            $user = $this->handler->getUserByIdentifier($identifier);

            if (null == $user) {
                $form->addError(new FormError(
                    $this->translator->trans('request_password.unknown_user', [], 'Admin')
                ));
                return;
            } else {
                $data->setUser($user);

                if ($user->getIsAlreadyRequested() && null != $user->getConfirmationToken()) {
                    $form->addError(new FormError('request_password.already_requested'));
                    return;
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Caramia\AdminBundle\User\Password\RequestPassword',
            'translation_domain' => 'Admin'
        ]);
    }

    public function getName()
    {
        return 'request_password_form';
    }
}
