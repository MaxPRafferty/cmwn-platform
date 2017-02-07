<?php

namespace User;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class UpdateEmailValidatorFactory
 */
class UpdateEmailValidatorFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UpdateEmailValidator([], $container->get(UserServiceInterface::class));
    }
}
