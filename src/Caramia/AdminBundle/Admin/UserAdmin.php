<?php

namespace Caramia\AdminBundle\Admin;

use Caramia\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UserAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('lastname')
            ->add('firstname')
            ->add('email')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('email')
            ->add('isActive')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email')
            ->add('lastname')
            ->add('firstname')
            ->add('plainPassword', 'text', array(
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
            ))
            ->add('isActive')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('email')
            ->add('lastname')
            ->add('firstname')
            ->add('isActive')
        ;
    }

    public function getUserManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('caramia_admin.user.manager');
    }

    public function postUpdate($object)
    {
        parent::preUpdate($object);
        $this->updateUser($object);
    }

    public function prePersist($object)
    {
        parent::prePersist($object);
        $object->setRoles(['ROLE_ADMIN', 'ROLE_SONATA_ADMIN']);
        $this->updateUser($object);
    }

    public function updateUser($object)
    {
        $password = $this->getForm()->get('plainPassword')->getData();
        if (strlen($password)) {
            $this->getUserManager()->updateCredentials($object, $password);
        }
    }
}
