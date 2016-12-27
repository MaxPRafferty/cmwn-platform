<?php

namespace Rule\Action\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Creates the action manager
 */
class ActionManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config       = $container->get('Config');
        $actionConfig = $config['actions'] ?? [];

        return new ActionManager($container, $actionConfig);
    }
}
