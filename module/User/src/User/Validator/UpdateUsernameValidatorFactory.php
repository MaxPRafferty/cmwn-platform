<?php

namespace User\Validator;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UpdateUsernameValidatorFactory
 */
class UpdateUsernameValidatorFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UpdateUsernameValidator([], $container->get(UserServiceInterface::class));
    }
}
