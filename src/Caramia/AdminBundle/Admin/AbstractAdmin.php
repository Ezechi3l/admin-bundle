<?php

namespace Caramia\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAbstractAdmin;

abstract class AbstractAdmin extends BaseAbstractAdmin
{
    public function breadCrumbsItem($object)
    {
        return parent::toString($object);
    }
}
