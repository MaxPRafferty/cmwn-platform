<?php

namespace Api\Factory;

use Api\Listeners\InjectUserListener;
use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class InjectUserListenerFactory
 * @package Api\Factory
 */
class InjectUserListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new InjectUserListener(
            $container->get(UserServiceInterface::class)
        );
    }
}
