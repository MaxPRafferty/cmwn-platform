<?php

namespace Import\Controller;

use Group\Service\GroupServiceInterface;
use Job\Service\JobServiceInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class UploadControllerFactory
 */
class UploadControllerFactory
{
    /**
     * @param \Zend\ServiceManager\containerInterface $container
     *
     * @return UploadController
     */
    public function __invoke($container)
    {
        // TODO zf3 Remove
        $container = $container instanceof ServiceLocatorAwareInterface
            ? $container->getServiceLocator()
            : $container;

        /** @var InputFilterPluginManager $inputFilterManager */
        $inputFilterManager = $container->get('InputFilterManager');

        /** @var InputFilterInterface $inputFilter */
        $inputFilter = $inputFilterManager->get('Api\\V1\\Rest\\Import\\Validator');

        return new UploadController(
            $inputFilter,
            $container,
            $container->get(JobServiceInterface::class),
            $container->get(GroupServiceInterface::class),
            $container->get(AuthenticationServiceInterface::class)
        );
    }
}
