<?php

namespace Caramia\AdminBundle\Block\Service;

use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Doctrine\Common\Persistence\ObjectManager;


class ShortListBlockService extends AbstractBlockService
{
    protected $objectManager;

    protected $pool;

    public function __construct($name = null, EngineInterface $templating = null, ObjectManager $objectManager, Pool $pool)
    {
        parent::__construct($name, $templating);

        $this->objectManager = $objectManager;

        $this->pool = $pool;
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'shortlist',
            'template' => 'CaramiaAdminBundle:Block:block_short-list.html.twig',
            'entity'   => null,
            'limit'    => 5,
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $admin = null;

        if ($settings['entity']) {
            // get number of entities of defined class

            if (class_exists($settings['entity'])) {
                $repo = $this->objectManager->getRepository($settings['entity']);
                $items = $repo->findBy([], ['createdAt' => 'DESC'], $settings['limit']);
                $admin = $this->pool->getAdminByClass($settings['entity']);
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
            'admin'     => $admin,
            'items'     => $items,
        ), $response);
    }
}
