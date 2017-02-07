<?php

namespace Caramia\AdminBundle\Block\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Doctrine\Common\Persistence\ObjectManager;


class CounterBlockService extends AbstractBlockService
{
    protected $objectManager;

    /**
     * @param String          $name
     * @param EngineInterface $templating
     * @param ObjectManager   $manager
     */
    public function __construct($name = null, EngineInterface $templating = null, ObjectManager $objectManager)
    {
        parent::__construct($name, $templating);

        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => 'counter',
            'template' => 'CaramiaAdminBundle:Block:block_counter.html.twig',
            'entity' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        $nb = 0;

        if ($settings['entity']) {
            // get number of entities of defined class

            if (class_exists($settings['entity'])) {
                $repo = $this->objectManager->getRepository($settings['entity']);
                $nb = count($repo->findAll());
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
            'nb'        => $nb,
        ), $response);
    }

}
