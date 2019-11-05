<?php

namespace Caramia\AdminBundle\Admin;

use Caramia\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Knp\Menu\ItemInterface;

use Caramia\AdminBundle\Entity\IpAdmin as Ip;

class IpAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('label', 'text', array(
                'label' => 'Libellé',
            ))
            ->add('ip', 'text', array(
                'label' => 'Adresse IP'
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('label');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('label', array(), array(
                'label' => 'Libellé',
            ))
            ->add('ip', array(), array(
                'label' => 'Adresse IP',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                )
            ));
    }

    public function toString($object)
    {
        if (!$object) {
            return '';
        }

        return sprintf('l’adresse IP "%s"', $object->getLabel());
    }

    public function breadCrumbsItem($object)
    {
        if (!$object) {
            return parent::toString($object);
        }

        return sprintf('Adresse IP "%s"', $object->getLabel());
    }
}
